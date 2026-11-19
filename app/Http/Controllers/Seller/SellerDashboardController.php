<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SellerDashboardController extends Controller
{
    public function dashboard(): View
    {
        $seller = Auth::user();
        $canteen = $seller->canteen;

        if (!$canteen) {
            abort(403, 'Akun Seller Anda belum terhubung dengan kantin.');
        }

        $today = Carbon::today();

        // Statistik Hari Ini khusus kantin seller ini
        $todayOrders = Order::where('canteen_id', $canteen->id)
            ->whereDate('created_at', $today);

        $totalToday = (clone $todayOrders)->count();
        $preparingCount = (clone $todayOrders)->where('status', 'Disiapkan')->count();
        $readyCount = (clone $todayOrders)->where('status', 'Siap diambil')->count();

        // Pendapatan hari ini
        $cashRevenue = Order::where('canteen_id', $canteen->id)
            ->where('payment_method', 'Tunai')
            ->where('payment_status', 'Lunas')
            ->whereDate('updated_at', $today)
            ->sum('total_price');

        $qrisRevenue = Order::where('canteen_id', $canteen->id)
            ->where('payment_method', 'QRIS')
            ->where('payment_status', 'Lunas')
            ->whereDate('paid_at', $today)
            ->sum('total_price');

        // Antrean pesanan hari ini yang belum selesai (Diterima, Disiapkan, Siap diambil)
        $queueOrders = Order::where('canteen_id', $canteen->id)
            ->whereDate('created_at', $today)
            ->orderByRaw("CASE status WHEN 'Diterima' THEN 1 WHEN 'Disiapkan' THEN 2 WHEN 'Siap diambil' THEN 3 ELSE 4 END")
            ->orderBy('id', 'desc')
            ->limit(15)
            ->get();

        return view('seller.dashboard', compact(
            'canteen',
            'totalToday',
            'preparingCount',
            'readyCount',
            'cashRevenue',
            'qrisRevenue',
            'queueOrders'
        ));
    }

    public function toggleOperationalStatus(Request $request): RedirectResponse
    {
        $canteen = Auth::user()->canteen;
        if (!$canteen) {
            abort(403);
        }

        $canteen->update(['is_open' => !$canteen->is_open]);
        $statusText = $canteen->is_open ? 'dibuka dan siap melayani pesanan' : 'ditutup sementara';

        ActivityLog::create([
            'user_id' => Auth::id(),
            'canteen_id' => $canteen->id,
            'action' => 'CANTEEN_STATUS_TOGGLED',
            'description' => "Penjual {$canteen->seller->name} mengubah status operasional kantin menjadi {$statusText}.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "Status operasional kantin berhasil diubah: Kantin sekarang {$statusText}.");
    }
}
