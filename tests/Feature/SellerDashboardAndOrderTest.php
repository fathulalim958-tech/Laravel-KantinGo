<?php

namespace Tests\Feature;

use App\Models\Canteen;
use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Tests\TestCase;

class SellerDashboardAndOrderTest extends TestCase
{
    protected function getSellerA(): User
    {
        return User::where('username', 'seller_viktor')->first();
    }

    protected function getSellerB(): User
    {
        return User::where('username', 'seller_witana')->first();
    }

    public function test_seller_can_access_dashboard_and_see_canteen_stats(): void
    {
        $seller = $this->getSellerA();

        $response = $this->actingAs($seller)->get(route('seller.dashboard'));
        $response->assertStatus(200);
        $response->assertSee($seller->canteen->name);
        $response->assertSee('Pesanan Hari Ini');
    }

    public function test_seller_can_toggle_canteen_operational_status(): void
    {
        $seller = $this->getSellerA();
        $initialStatus = $seller->canteen->is_open;

        $response = $this->actingAs($seller)->post(route('seller.toggle-status'));
        $response->assertRedirect();

        $seller->canteen->refresh();
        $this->assertEquals(!$initialStatus, $seller->canteen->is_open);
    }

    public function test_seller_can_advance_order_status(): void
    {
        $seller = $this->getSellerA();
        $order = Order::where('canteen_id', $seller->canteen->id)->where('status', 'Diterima')->first();

        $response = $this->actingAs($seller)->post(route('seller.orders.status', $order), [
            'expected' => 'Diterima',
        ]);

        $response->assertRedirect();
        $order->refresh();
        $this->assertEquals('Disiapkan', $order->status);
    }

    public function test_seller_completing_cash_order_records_payment_as_lunas(): void
    {
        $seller = $this->getSellerA();
        $order = Order::where('canteen_id', $seller->canteen->id)
            ->where('payment_method', 'Tunai')
            ->where('status', 'Siap diambil')
            ->first();

        if ($order) {
            $response = $this->actingAs($seller)->post(route('seller.orders.status', $order), [
                'expected' => 'Siap diambil',
            ]);

            $response->assertRedirect();
            $order->refresh();
            $this->assertEquals('Selesai', $order->status);
            $this->assertEquals('Lunas', $order->payment_status);
            $this->assertNotNull($order->payment);
        }
    }

    public function test_seller_a_cannot_view_or_update_seller_b_order(): void
    {
        $sellerA = $this->getSellerA();
        $sellerB = $this->getSellerB();

        // Cari atau buat pesanan untuk Kantin B
        $orderB = Order::firstOrCreate(
            ['order_code' => 'TEST-B-999'],
            [
                'canteen_id' => $sellerB->canteen->id,
                'customer_name' => 'Testing Isolasi Seller',
                'customer_type' => 'Umum',
                'detail_menu_snapshot' => [],
                'total_price' => 15000,
                'status' => 'Diterima',
                'payment_method' => 'Tunai',
                'payment_status' => 'Belum dibayar',
                'access_token' => 'token_test_b_' . uniqid(),
                'checkout_key' => 'checkout_b_' . uniqid(),
            ]
        );

        // Seller A mencoba melihat pesanan Kantin B
        $responseView = $this->actingAs($sellerA)->get(route('seller.orders.show', $orderB));
        $responseView->assertStatus(403);

        // Seller A mencoba update status pesanan Kantin B
        $responseUpdate = $this->actingAs($sellerA)->post(route('seller.orders.status', $orderB), [
            'expected' => 'Diterima',
        ]);
        $responseUpdate->assertStatus(403);
    }

    public function test_seller_can_create_update_and_delete_menu(): void
    {
        $seller = $this->getSellerA();

        // 1. Create Menu
        $responseCreate = $this->actingAs($seller)->post(route('seller.menus.store'), [
            'name' => 'Menu Khusus Testing Seller',
            'kategori' => 'Makanan',
            'price' => 18000,
            'stock' => 25,
            'description' => 'Menu pengujian otomatis',
        ]);

        $responseCreate->assertRedirect(route('seller.menus.index'));
        $menu = Menu::where('name', 'Menu Khusus Testing Seller')->first();
        $this->assertNotNull($menu);
        $this->assertEquals($seller->canteen->id, $menu->canteen_id);

        // 2. Update Menu
        $responseUpdate = $this->actingAs($seller)->put(route('seller.menus.update', $menu), [
            'name' => 'Menu Khusus Testing Seller Updated',
            'kategori' => 'Makanan',
            'price' => 20000,
            'stock' => 30,
            'is_active' => 1,
        ]);

        $responseUpdate->assertRedirect(route('seller.menus.index'));
        $menu->refresh();
        $this->assertEquals(20000, $menu->price);

        // 3. Delete Menu (Soft Delete)
        $responseDelete = $this->actingAs($seller)->delete(route('seller.menus.destroy', $menu));
        $responseDelete->assertRedirect(route('seller.menus.index'));
        $this->assertSoftDeleted('menus', ['id' => $menu->id]);
    }

    public function test_seller_a_cannot_edit_seller_b_menu(): void
    {
        $sellerA = $this->getSellerA();
        $sellerB = $this->getSellerB();

        $menuB = Menu::where('canteen_id', $sellerB->canteen->id)->first();

        // Seller A mencoba edit menu Seller B
        $responseEdit = $this->actingAs($sellerA)->get(route('seller.menus.edit', $menuB));
        $responseEdit->assertStatus(403);

        // Seller A mencoba update menu Seller B
        $responseUpdate = $this->actingAs($sellerA)->put(route('seller.menus.update', $menuB), [
            'name' => 'Hack Menu B',
            'kategori' => 'Makanan',
            'price' => 1000,
            'stock' => 1,
        ]);
        $responseUpdate->assertStatus(403);
    }

    public function test_seller_can_view_sales_report(): void
    {
        $seller = $this->getSellerA();

        $response = $this->actingAs($seller)->get(route('seller.reports.index'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Omzet');
        $response->assertSee('Menu Terlaris');
    }
}
