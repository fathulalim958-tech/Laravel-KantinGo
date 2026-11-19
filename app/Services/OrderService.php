<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Canteen;
use App\Models\DailyTransaction;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class OrderService
{
    /**
     * Membuat pesanan baru secara atomik dengan database transaction & lockForUpdate.
     */
    public function createOrder(
        Canteen $canteen,
        ?User $customer,
        array $cartItems,
        string $customerName,
        ?string $customerType,
        string $paymentMethod,
        string $checkoutKey
    ): Order {
        if (empty($cartItems)) {
            throw new RuntimeException('Keranjang belanja Anda masih kosong.');
        }

        // Cek idempotensi: jika checkout_key sudah pernah diproses, kembalikan pesanan yang sudah ada
        $existingOrder = Order::where('checkout_key', $checkoutKey)->first();
        if ($existingOrder) {
            return $existingOrder;
        }

        return DB::transaction(function () use (
            $canteen,
            $customer,
            $cartItems,
            $customerName,
            $customerType,
            $paymentMethod,
            $checkoutKey
        ) {
            $menuIds = array_keys($cartItems);

            // Lock baris menu di database agar stok tidak terambil secara bersamaan oleh transaksi lain
            $menus = Menu::where('canteen_id', $canteen->id)
                ->whereIn('id', $menuIds)
                ->where('is_active', true)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($menus->count() !== count($menuIds)) {
                throw new RuntimeException('Salah satu menu sudah tidak tersedia atau telah dihapus oleh penjual.');
            }

            $detailSnapshot = [];
            $totalPrice = 0;
            $itemsToCreate = [];

            foreach ($cartItems as $menuId => $qty) {
                $qty = (int)$qty;
                if ($qty < 1) continue;

                /** @var Menu $menu */
                $menu = $menus->get($menuId);
                if (!$menu) {
                    throw new RuntimeException('Menu tidak ditemukan.');
                }

                if ($menu->stock < $qty) {
                    throw new RuntimeException("Stok \"{$menu->name}\" tersisa {$menu->stock} porsi. Mohon kurangi jumlah pesanan.");
                }

                // Kurangi stok menu secara langsung
                $menu->decrement('stock', $qty);

                $subtotal = $menu->price * $qty;
                $totalPrice += $subtotal;

                $detailSnapshot[] = [
                    'id' => $menu->id,
                    'nama_menu' => $menu->name,
                    'harga' => $menu->price,
                    'jumlah' => $qty,
                    'subtotal' => $subtotal,
                ];

                $itemsToCreate[] = [
                    'menu_id' => $menu->id,
                    'menu_name' => $menu->name,
                    'price' => $menu->price,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ];
            }

            // Generate Kode Pesanan Berurutan Unik (e.g. A-027, A-028)
            $lastOrderCode = Order::where('order_code', 'like', 'A-%')
                ->selectRaw("MAX(CAST(SUBSTRING(order_code, 3) AS UNSIGNED)) as max_num")
                ->value('max_num');

            $nextNumber = max((int)$lastOrderCode + 1, Order::count() + 1);
            $orderCode = sprintf('A-%03d', $nextNumber);

            $accessToken = Str::random(32);

            $order = Order::create([
                'canteen_id' => $canteen->id,
                'customer_id' => $customer?->id,
                'order_code' => $orderCode,
                'customer_name' => $customerName,
                'customer_type' => $customerType ?? ($customer ? ucfirst($customer->user_type ?? 'Pelanggan') : 'Umum'),
                'detail_menu_snapshot' => $detailSnapshot,
                'total_price' => $totalPrice,
                'status' => 'Diterima',
                'payment_method' => $paymentMethod,
                'payment_status' => 'Belum dibayar',
                'access_token' => $accessToken,
                'checkout_key' => $checkoutKey,
            ]);

            foreach ($itemsToCreate as $itemData) {
                $order->items()->create($itemData);
            }

            ActivityLog::create([
                'user_id' => $customer?->id,
                'canteen_id' => $canteen->id,
                'action' => 'ORDER_CREATED',
                'description' => "Pesanan baru dibuat: {$order->order_code} senilai Rp" . number_format($order->total_price, 0, ',', '.') . " ({$paymentMethod})",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $order;
        });
    }

    /**
     * Mencatat pembayaran QRIS simulasi dan mengupdate status pesanan.
     */
    public function recordPayment(Order $order, string $method): void
    {
        DB::transaction(function () use ($order, $method) {
            // Cek apakah pesanan sudah lunas untuk idempotensi
            $existingPayment = Payment::where('order_id', $order->id)->lockForUpdate()->first();
            if ($existingPayment) {
                return;
            }

            $now = Carbon::now();

            Payment::create([
                'order_id' => $order->id,
                'method' => $method,
                'amount' => $order->total_price,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $order->update([
                'payment_status' => 'Lunas',
                'paid_at' => $now,
                // Jika pesanan masih berstatus Diterima dan QRIS, naikkan otomatis ke Disiapkan
                'status' => ($method === 'QRIS' && $order->status === 'Diterima') ? 'Disiapkan' : $order->status,
            ]);

            // Agregasi ledger ke daily_transactions
            $date = $now->toDateString();
            $daily = DailyTransaction::where('canteen_id', $order->canteen_id)
                ->where('date', $date)
                ->where('payment_method', $method)
                ->lockForUpdate()
                ->first();

            if ($daily) {
                $daily->increment('total_revenue', $order->total_price);
                $daily->increment('transaction_count', 1);
            } else {
                DailyTransaction::create([
                    'canteen_id' => $order->canteen_id,
                    'date' => $date,
                    'payment_method' => $method,
                    'total_revenue' => $order->total_price,
                    'transaction_count' => 1,
                ]);
            }

            ActivityLog::create([
                'user_id' => auth()->id() ?? $order->customer_id,
                'canteen_id' => $order->canteen_id,
                'action' => 'PAYMENT_RECORDED',
                'description' => "Pembayaran {$method} lunas untuk pesanan {$order->order_code} senilai Rp" . number_format($order->total_price, 0, ',', '.'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
    }
}
