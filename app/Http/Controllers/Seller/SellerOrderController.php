<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use RuntimeException;

class SellerOrderController extends Controller
{
    public function index(Request $request): View
    {
        $canteen = Auth::user()->canteen;
        if (!$canteen) abort(403);

        $query = Order::where('canteen_id', $canteen->id)->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('seller.orders.index', compact('canteen', 'orders'));
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        $order->load(['canteen', 'items']);

        return view('seller.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order, OrderService $orderService): RedirectResponse
    {
        $this->authorize('updateStatus', $order);

        $expectedStatus = $request->input('expected');
        if ($order->status !== $expectedStatus) {
            return back()->with('error', 'Status pesanan sudah berubah sebelumnya. Silakan muat ulang halaman.');
        }

        if ($order->status === 'Selesai') {
            return back()->with('info', 'Pesanan ini sudah selesai.');
        }

        // Jika QRIS dan belum dibayar, seller tidak bisa memproses
        if ($order->payment_method === 'QRIS' && $order->payment_status !== 'Lunas') {
            return back()->with('error', 'Menunggu simulasi pembayaran QRIS dari pembeli.');
        }

        $nextMap = [
            'Diterima' => 'Disiapkan',
            'Disiapkan' => 'Siap diambil',
            'Siap diambil' => 'Selesai',
        ];

        $nextStatus = $nextMap[$order->status] ?? null;
        if (!$nextStatus) {
            return back()->with('error', 'Transisi status tidak valid.');
        }

        // Jika transisi ke Selesai dan Tunai, catat pembayaran tunai lunas
        if ($nextStatus === 'Selesai' && $order->payment_method === 'Tunai' && $order->payment_status !== 'Lunas') {
            $orderService->recordPayment($order, 'Tunai');
        }

        $order->update(['status' => $nextStatus]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'canteen_id' => $order->canteen_id,
            'action' => 'ORDER_STATUS_UPDATED',
            'description' => "Penjual memperbarui status pesanan {$order->order_code} menjadi {$nextStatus}.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "Pesanan {$order->order_code} berhasil diperbarui menjadi {$nextStatus}!");
    }
}
