<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CanteenController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\Admin\TransactionMonitoringController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Customer\CanteenCatalogController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\OrderTrackingController;
use App\Http\Controllers\Seller\SellerDashboardController;
use App\Http\Controllers\Seller\SellerMenuController;
use App\Http\Controllers\Seller\SellerOrderController;
use App\Http\Controllers\Seller\SellerReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - KantinGO UNPAM
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'seller' => redirect()->route('seller.dashboard'),
            default => redirect()->route('customer.canteens'),
        };
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ==========================================
// ORDER TRACKING & QRIS (Token Privat Publik)
// ==========================================
Route::get('/orders/track/{token}', [OrderTrackingController::class, 'track'])->name('orders.track');
Route::get('/orders/qris/{token}', [OrderTrackingController::class, 'qrisSimulate'])->name('orders.qris');
Route::post('/orders/qris/{token}/pay', [OrderTrackingController::class, 'qrisPay'])->name('orders.qris.pay');
Route::post('/orders/track/{token}/complete', [OrderTrackingController::class, 'markAsCompleted'])->name('orders.complete');
Route::get('/api/orders/track/{token}', [OrderTrackingController::class, 'apiStatus'])->name('api.orders.track');

// ==========================================
// ADMIN ROUTES (Role: admin)
// ==========================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Kantin
    Route::post('/canteens/{canteen}/toggle-status', [CanteenController::class, 'toggleStatus'])->name('canteens.toggle-status');
    Route::resource('canteens', CanteenController::class);

    // Manajemen Penjual (Seller)
    Route::post('/sellers/{seller}/toggle-status', [SellerController::class, 'toggleStatus'])->name('sellers.toggle-status');
    Route::resource('sellers', SellerController::class)->except(['show']);

    // Manajemen Pelanggan (Customer)
    Route::post('/customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
    Route::resource('customers', CustomerController::class)->except(['create', 'store', 'destroy']);

    // Monitoring Transaksi Platform
    Route::get('/transactions', [TransactionMonitoringController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{order}', [TransactionMonitoringController::class, 'show'])->name('transactions.show');

    // Log Aktivitas & Audit Trail
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});

// ==========================================
// SELLER ROUTES (Role: seller)
// ==========================================
Route::prefix('seller')->name('seller.')->middleware(['auth', 'role:seller'])->group(function () {
    Route::get('/dashboard', [SellerDashboardController::class, 'dashboard'])->name('dashboard');
    Route::post('/toggle-status', [SellerDashboardController::class, 'toggleOperationalStatus'])->name('toggle-status');

    // Antrean & Detail Pesanan
    Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [SellerOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [SellerOrderController::class, 'updateStatus'])->name('orders.status');

    // Manajemen Menu & Stok
    Route::resource('menus', SellerMenuController::class)->except(['show']);

    // Laporan Penjualan
    Route::get('/reports', [SellerReportController::class, 'index'])->name('reports.index');
});

// ==========================================
// CUSTOMER ROUTES (Role: customer)
// ==========================================
Route::prefix('customer')->name('customer.')->middleware(['auth', 'role:customer'])->group(function () {
    // Katalog Multi-Kantin & Detail Stan
    Route::get('/canteens', [CanteenCatalogController::class, 'index'])->name('canteens');
    Route::get('/canteens/{canteen:slug}', [CanteenCatalogController::class, 'show'])->name('canteens.show');

    // Shopping Cart Multi-Kantin
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Atomic Checkout
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

    // Riwayat Pesanan Customer
    Route::get('/orders', [OrderTrackingController::class, 'history'])->name('orders.index');
});
