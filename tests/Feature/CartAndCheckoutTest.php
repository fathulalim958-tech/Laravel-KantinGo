<?php

namespace Tests\Feature;

use App\Models\Canteen;
use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class CartAndCheckoutTest extends TestCase
{
    protected function getCustomer(): User
    {
        return User::where('role', 'customer')->first();
    }

    public function test_customer_can_add_menu_to_cart(): void
    {
        $menu = Menu::where('stock', '>', 5)->first();

        $response = $this->actingAs($this->getCustomer())->post(route('customer.cart.add'), [
            'menu_id' => $menu->id,
            'quantity' => 2,
        ]);

        $response->assertRedirect();
        $this->assertEquals(2, session('cart')[$menu->id]);
        $this->assertEquals($menu->canteen_id, session('cart_canteen_id'));
    }

    public function test_multi_canteen_conflict_triggers_warning(): void
    {
        $canteenA = Canteen::first();
        $canteenB = Canteen::where('id', '!=', $canteenA->id)->first();

        $menuA = Menu::where('canteen_id', $canteenA->id)->where('stock', '>', 5)->first();
        $menuB = Menu::where('canteen_id', $canteenB->id)->where('stock', '>', 5)->first();

        // 1. Masukkan menu dari Kantin A
        $this->actingAs($this->getCustomer())->post(route('customer.cart.add'), [
            'menu_id' => $menuA->id,
            'quantity' => 1,
        ]);

        // 2. Coba masukkan menu dari Kantin B tanpa switch_canteen
        $response = $this->actingAs($this->getCustomer())->post(route('customer.cart.add'), [
            'menu_id' => $menuB->id,
            'quantity' => 1,
        ]);

        $response->assertSessionHas('canteen_conflict');
        $this->assertEquals($canteenA->id, session('cart_canteen_id'));

        // 3. Masukkan menu dari Kantin B dengan switch_canteen = 1
        $responseSwitch = $this->actingAs($this->getCustomer())->post(route('customer.cart.add'), [
            'menu_id' => $menuB->id,
            'quantity' => 1,
            'switch_canteen' => 1,
        ]);

        $responseSwitch->assertSessionMissing('canteen_conflict');
        $this->assertEquals($canteenB->id, session('cart_canteen_id'));
        $this->assertArrayHasKey($menuB->id, session('cart'));
        $this->assertArrayNotHasKey($menuA->id, session('cart'));
    }

    public function test_atomic_checkout_cash_reduces_stock_and_creates_order(): void
    {
        $menu = Menu::where('stock', '>', 10)->first();
        $initialStock = $menu->stock;
        $orderQty = 2;

        // Siapkan sesi keranjang
        session()->put('cart', [$menu->id => $orderQty]);
        session()->put('cart_canteen_id', $menu->canteen_id);
        $checkoutKey = Str::random(32);

        $response = $this->actingAs($this->getCustomer())->post(route('customer.checkout.process'), [
            'nama_pengambil' => 'Budi Pengujian Atomik',
            'customer_type' => 'Mahasiswa TI',
            'metode_pembayaran' => 'Tunai',
            'checkout_key' => $checkoutKey,
        ]);

        $order = Order::where('checkout_key', $checkoutKey)->first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('orders.track', $order->access_token));

        // Verifikasi stok berkurang
        $menu->refresh();
        $this->assertEquals($initialStock - $orderQty, $menu->stock);

        // Verifikasi sesi keranjang dibersihkan
        $this->assertEmpty(session('cart'));
    }

    public function test_checkout_idempotency_prevents_duplicate_orders(): void
    {
        $menu = Menu::where('stock', '>', 10)->first();
        $checkoutKey = 'idempotent_test_' . uniqid();

        session()->put('cart', [$menu->id => 1]);
        session()->put('cart_canteen_id', $menu->canteen_id);

        // Request pertama
        $this->actingAs($this->getCustomer())->post(route('customer.checkout.process'), [
            'nama_pengambil' => 'Pengujian Idempotensi',
            'metode_pembayaran' => 'Tunai',
            'checkout_key' => $checkoutKey,
        ]);

        $orderCount = Order::where('checkout_key', $checkoutKey)->count();
        $this->assertEquals(1, $orderCount);

        // Request kedua dengan kunci checkout yang sama
        session()->put('cart', [$menu->id => 1]);
        session()->put('cart_canteen_id', $menu->canteen_id);

        $this->actingAs($this->getCustomer())->post(route('customer.checkout.process'), [
            'nama_pengambil' => 'Pengujian Idempotensi 2',
            'metode_pembayaran' => 'Tunai',
            'checkout_key' => $checkoutKey,
        ]);

        // Tidak boleh bertambah
        $this->assertEquals(1, Order::where('checkout_key', $checkoutKey)->count());
    }

    public function test_qris_checkout_and_simulation_payment(): void
    {
        $menu = Menu::where('stock', '>', 5)->first();
        $checkoutKey = 'qris_test_' . uniqid();

        session()->put('cart', [$menu->id => 1]);
        session()->put('cart_canteen_id', $menu->canteen_id);

        $response = $this->actingAs($this->getCustomer())->post(route('customer.checkout.process'), [
            'nama_pengambil' => 'Pembeli QRIS',
            'metode_pembayaran' => 'QRIS',
            'checkout_key' => $checkoutKey,
        ]);

        $order = Order::where('checkout_key', $checkoutKey)->first();
        $response->assertRedirect(route('orders.qris', $order->access_token));

        // Eksekusi simulasi bayar
        $payResponse = $this->post(route('orders.qris.pay', $order->access_token));
        $payResponse->assertRedirect(route('orders.track', $order->access_token));

        $order->refresh();
        $this->assertEquals('Lunas', $order->payment_status);
        $this->assertEquals('Disiapkan', $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertNotNull($order->payment);
    }

    public function test_api_tracking_status_returns_valid_json(): void
    {
        $order = Order::first();

        $response = $this->get(route('api.orders.track', $order->access_token));
        $response->assertStatus(200);
        $response->assertJson([
            'order_code' => $order->order_code,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
        ]);
    }
}
