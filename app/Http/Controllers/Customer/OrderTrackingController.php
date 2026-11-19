<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    /**
     * Pelacakan status pesanan publik/privat berbasis token rahasia.
     */
    public function track(string $token): View
    {
        $order = Order::where('access_token', $token)
            ->with(['canteen', 'items.menu'])
            ->firstOrFail();

        return view('customer.orders.track', compact('order'));
    }

    /**
     * Endpoint API JSON untuk auto-polling setiap 8 detik.
     */
    public function apiStatus(string $token): JsonResponse
    {
        $order = Order::where('access_token', $token)->first();

        if (!$order) {
            return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
        }

        return response()->json([
            'order_code' => $order->order_code,
            'status' => $order->status,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'paid_at' => $order->paid_at?->format('H:i:s d/m/Y'),
            'updated_at' => $order->updated_at->format('H:i:s'),
        ]);
    }

    /**
     * Halaman simulasi QRIS (dengan label DEMO / BUKAN QRIS).
     */
    public function qrisSimulate(string $token): View|RedirectResponse
    {
        $order = Order::where('access_token', $token)->with('canteen')->firstOrFail();

        if ($order->payment_method !== 'QRIS') {
            return redirect()->route('orders.track', $order->access_token);
        }

        return view('customer.orders.qris', compact('order'));
    }

    /**
     * Memproses simulasi pembayaran QRIS sukses.
     */
    public function qrisPay(Request $request, string $token, OrderService $orderService): RedirectResponse
    {
        $order = Order::where('access_token', $token)->firstOrFail();

        if ($order->payment_method !== 'QRIS') {
            return redirect()->route('orders.track', $order->access_token);
        }

        if ($order->payment_status === 'Lunas') {
            return redirect()->route('orders.track', $order->access_token)
                ->with('info', 'Pesanan ini sudah dinyatakan lunas.');
        }

        $orderService->recordPayment($order, 'QRIS');

        return redirect()->route('orders.track', $order->access_token)
            ->with('success', 'Simulasi pembayaran QRIS berhasil! Pesanan Anda telah ditandai lunas dan mulai disiapkan oleh penjual.');
    }

    /**
     * Pelanggan menyelesaikan pesanan / konfirmasi sudah diambil.
     */
    public function markAsCompleted(Request $request, string $token, OrderService $orderService): RedirectResponse
    {
        $order = Order::where('access_token', $token)->firstOrFail();

        if ($order->status !== 'Siap diambil') {
            return back()->with('error', 'Hanya pesanan yang sudah berstatus "Siap diambil" yang dapat diselesaikan.');
        }

        // Pesanan dengan pembayaran tunai harus diselesaikan langsung oleh penjual di kasir
        if (in_array(strtolower($order->payment_method), ['tunai', 'cash'])) {
            return back()->with('error', 'Pesanan dengan pembayaran tunai di kasir hanya dapat diselesaikan langsung oleh pihak penjual kantin.');
        }

        $order->update(['status' => 'Selesai']);

        ActivityLog::create([
            'user_id' => Auth::id() ?? $order->customer_id,
            'canteen_id' => $order->canteen_id,
            'action' => 'ORDER_COMPLETED_BY_CUSTOMER',
            'description' => "Pelanggan ({$order->customer_name}) mengonfirmasi pesanan {$order->order_code} telah diambil/selesai.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "Terima kasih! Pesanan {$order->order_code} telah dikonfirmasi selesai dan sudah Anda ambil.");
    }

    /**
     * Riwayat pesanan milik customer yang sedang login.
     */
    public function history(Request $request): View
    {
        $query = Order::where('customer_id', Auth::id())
            ->with(['canteen', 'items'])
            ->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('customer.orders.history', compact('orders'));
    }
}
