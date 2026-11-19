<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SellerReportController extends Controller
{
    public function index(Request $request): View
    {
        $canteen = Auth::user()->canteen;
        if (!$canteen) abort(403);

        $now = Carbon::now();

        // 1. Total Omzet & Pesanan Lunas (All-time)
        $allOrders = Order::where('canteen_id', $canteen->id)->where('payment_status', 'Lunas');
        $allTimeRevenue = (clone $allOrders)->sum('total_price');
        $allTimeOrders = (clone $allOrders)->count();

        // 2. Omzet Bulan Ini
        $thisMonthOrders = Order::where('canteen_id', $canteen->id)
            ->where('payment_status', 'Lunas')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year);
        $monthRevenue = (clone $thisMonthOrders)->sum('total_price');
        $monthOrders = (clone $thisMonthOrders)->count();

        // 3. Omzet Minggu Ini
        $thisWeekOrders = Order::where('canteen_id', $canteen->id)
            ->where('payment_status', 'Lunas')
            ->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
        $weekRevenue = (clone $thisWeekOrders)->sum('total_price');
        $weekOrders = (clone $thisWeekOrders)->count();

        // 4. Breakdown Metode Pembayaran (Tunai vs QRIS)
        $cashTotal = Order::where('canteen_id', $canteen->id)->where('payment_method', 'Tunai')->where('payment_status', 'Lunas')->sum('total_price');
        $qrisTotal = Order::where('canteen_id', $canteen->id)->where('payment_method', 'QRIS')->where('payment_status', 'Lunas')->sum('total_price');

        // 5. Menu Terlaris (Best Seller)
        $bestSellers = OrderItem::select('menu_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('order', function ($q) use ($canteen) {
                $q->where('canteen_id', $canteen->id)->where('payment_status', 'Lunas');
            })
            ->groupBy('menu_name')
            ->orderByDesc('total_qty')
            ->limit(8)
            ->get();

        return view('seller.reports.index', compact(
            'canteen',
            'allTimeRevenue',
            'allTimeOrders',
            'monthRevenue',
            'monthOrders',
            'weekRevenue',
            'weekOrders',
            'cashTotal',
            'qrisTotal',
            'bestSellers'
        ));
    }
}
