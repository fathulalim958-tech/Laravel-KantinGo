<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Canteen;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();

        // 1. Statistik Pengguna
        $totalUsers = User::count();
        $totalMahasiswa = User::where('user_type', 'mahasiswa')->count();
        $totalDosen = User::where('user_type', 'dosen')->count();
        $totalStaf = User::where('user_type', 'staf')->count();
        $totalSellers = User::where('role', 'seller')->count();

        // 2. Statistik Kantin & Menu
        $totalCanteens = Canteen::count();
        $openCanteens = Canteen::where('is_open', true)->count();
        $totalMenus = Menu::count();

        // 3. Statistik Transaksi & Pendapatan
        $totalOrders = Order::count();
        $todayOrdersCount = Order::whereDate('created_at', $today)->count();
        $totalRevenue = Payment::sum('amount');
        $todayRevenue = Payment::whereDate('created_at', $today)->sum('amount');

        // 4. Performa Kantin (Ranking Omzet)
        $canteenStats = Canteen::withCount('orders')
            ->with(['orders' => function ($q) {
                $q->where('payment_status', 'Lunas');
            }])
            ->get()
            ->map(function ($canteen) {
                $canteen->revenue = $canteen->orders->sum('total_price');
                return $canteen;
            })
            ->sortByDesc('revenue');

        // 5. Pesanan Terkini Lintas Kantin
        $recentOrders = Order::with(['canteen', 'customer'])->latest()->limit(8)->get();

        // 6. Log Aktivitas Terkini
        $recentLogs = ActivityLog::with('user')->latest('id')->limit(8)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalMahasiswa',
            'totalDosen',
            'totalStaf',
            'totalSellers',
            'totalCanteens',
            'openCanteens',
            'totalMenus',
            'totalOrders',
            'todayOrdersCount',
            'totalRevenue',
            'todayRevenue',
            'canteenStats',
            'recentOrders',
            'recentLogs'
        ));
    }
}
