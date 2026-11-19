<?php

namespace Tests\Feature;

use App\Models\Canteen;
use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Tests\TestCase;

class EndToEndIntegrationTest extends TestCase
{
    public function test_full_customer_ordering_qris_simulation_and_seller_fulfillment_lifecycle(): void
    {
        $customer = User::where('username', 'budi_mhs')->first();
        $seller = User::where('username', 'seller_viktor')->first();
        $canteen = $seller->canteen;
        $menu = Menu::where('canteen_id', $canteen->id)->where('stock', '>', 5)->first();

        $initialStock = $menu->stock;

        // 1. Customer adds item to cart
        $cartResponse = $this->actingAs($customer)->post(route('customer.cart.add'), [
            'menu_id' => $menu->id,
            'quantity' => 2,
        ]);
        $cartResponse->assertRedirect();

        // 2. Customer performs atomic checkout with QRIS
        $checkoutKey = 'e2e_qris_' . uniqid();
        $checkoutResponse = $this->actingAs($customer)->post(route('customer.checkout.process'), [
            'checkout_key' => $checkoutKey,
            'nama_pengambil' => 'Budi Mahasiswa E2E',
            'customer_type' => 'Mahasiswa',
            'metode_pembayaran' => 'QRIS',
        ]);

        $order = Order::where('checkout_key', $checkoutKey)->first();
        $this->assertNotNull($order);
        $this->assertEquals(2 * $menu->price, $order->total_price);
        $this->assertEquals('Belum dibayar', $order->payment_status);

        // Verify stock deducted
        $menu->refresh();
        $this->assertEquals($initialStock - 2, $menu->stock);

        // 3. Customer accesses QRIS simulation page
        $qrisPageResponse = $this->get(route('orders.qris', $order->access_token));
        $qrisPageResponse->assertStatus(200);
        $qrisPageResponse->assertSee('DEMO / BUKAN QRIS');

        // 4. Customer triggers QRIS demo payment
        $payResponse = $this->post(route('orders.qris.pay', $order->access_token));
        $payResponse->assertRedirect(route('orders.track', $order->access_token));

        $order->refresh();
        $this->assertEquals('Lunas', $order->payment_status);
        $this->assertEquals('Disiapkan', $order->status);
        $this->assertNotNull($order->payment);

        // 5. Seller logs in and views the order in their queue
        $sellerQueueResponse = $this->actingAs($seller)->get(route('seller.orders.index'));
        $sellerQueueResponse->assertStatus(200);
        $sellerQueueResponse->assertSee($order->order_code);

        // 6. Seller marks order as "Siap diambil"
        $advanceResponse1 = $this->actingAs($seller)->post(route('seller.orders.status', $order), [
            'expected' => 'Disiapkan',
        ]);
        $advanceResponse1->assertRedirect();
        $order->refresh();
        $this->assertEquals('Siap diambil', $order->status);

        // 7. Seller marks order as "Selesai"
        $advanceResponse2 = $this->actingAs($seller)->post(route('seller.orders.status', $order), [
            'expected' => 'Siap diambil',
        ]);
        $advanceResponse2->assertRedirect();
        $order->refresh();
        $this->assertEquals('Selesai', $order->status);

        // 8. Public live tracking API returns final completed state
        $apiResponse = $this->get(route('api.orders.track', $order->access_token));
        $apiResponse->assertStatus(200);
        $apiResponse->assertJson([
            'order_code' => $order->order_code,
            'status' => 'Selesai',
            'payment_status' => 'Lunas',
        ]);

        // 9. Admin monitors order in platform transactions
        $admin = User::where('role', 'admin')->first();
        $adminAuditResponse = $this->actingAs($admin)->get(route('admin.transactions.show', $order));
        $adminAuditResponse->assertStatus(200);
        $adminAuditResponse->assertSee($order->order_code);
        $adminAuditResponse->assertSee('Budi Mahasiswa E2E');
    }

    public function test_multi_canteen_cart_protection_and_switch_workflow(): void
    {
        $customer = User::where('username', 'budi_mhs')->first();
        $canteenA = Canteen::where('slug', 'kantin-utama-viktor')->first();
        $canteenB = Canteen::where('slug', 'kantin-maju-jaya-witana')->first();

        $menuA = Menu::where('canteen_id', $canteenA->id)->first();
        $menuB = Menu::where('canteen_id', $canteenB->id)->first();

        // 1. Add menu from Canteen A
        $this->actingAs($customer)->post(route('customer.cart.add'), [
            'menu_id' => $menuA->id,
            'quantity' => 1,
        ]);

        $this->assertEquals([$menuA->id => 1], session('cart'));

        // 2. Add menu from Canteen B without switch flag -> triggers warning conflict
        $conflictResponse = $this->actingAs($customer)->post(route('customer.cart.add'), [
            'menu_id' => $menuB->id,
            'quantity' => 1,
        ]);

        $conflictResponse->assertSessionHas('canteen_conflict');
        // Cart still holds only Canteen A item
        $this->assertEquals([$menuA->id => 1], session('cart'));

        // 3. Confirm switch to Canteen B
        $switchResponse = $this->actingAs($customer)->post(route('customer.cart.add'), [
            'menu_id' => $menuB->id,
            'quantity' => 2,
            'switch_canteen' => 1,
        ]);

        $switchResponse->assertSessionMissing('canteen_conflict');
        $this->assertEquals([$menuB->id => 2], session('cart'));
    }

    public function test_strict_multi_vendor_data_isolation(): void
    {
        $sellerA = User::where('username', 'seller_viktor')->first();
        $sellerB = User::where('username', 'seller_witana')->first();

        $canteenA = $sellerA->canteen;
        $canteenB = $sellerB->canteen;

        $menuA = Menu::where('canteen_id', $canteenA->id)->first();

        // Seller B cannot edit Seller A's menu
        $editResponse = $this->actingAs($sellerB)->get(route('seller.menus.edit', $menuA));
        $editResponse->assertStatus(403);

        $updateResponse = $this->actingAs($sellerB)->put(route('seller.menus.update', $menuA), [
            'name' => 'Menu Diretas Seller B',
            'price' => 1000,
            'stock' => 100,
            'is_available' => 1,
        ]);
        $updateResponse->assertStatus(403);

        // Seller B cannot view Seller A's orders in seller orders show
        $orderA = Order::where('canteen_id', $canteenA->id)->first();
        if ($orderA) {
            $viewOrderResponse = $this->actingAs($sellerB)->get(route('seller.orders.show', $orderA));
            $viewOrderResponse->assertStatus(403);

            $updateStatusResponse = $this->actingAs($sellerB)->post(route('seller.orders.status', $orderA), [
                'expected' => $orderA->status,
            ]);
            $updateStatusResponse->assertStatus(403);
        }
    }

    public function test_customer_cannot_mark_cash_order_as_completed(): void
    {
        $order = Order::whereDoesntHave('payment')->first();
        $order->update(['status' => 'Siap diambil', 'payment_method' => 'Tunai', 'payment_status' => 'Belum dibayar']);

        // Check that completion button is NOT visible for cash orders
        $trackResponse = $this->get(route('orders.track', $order->access_token));
        $trackResponse->assertStatus(200);
        $trackResponse->assertDontSee('Pesanan Selesai / Sudah Diambil');
        $trackResponse->assertSee('Pembayaran Tunai di Kasir');

        // Customer attempting to POST complete is blocked
        $completeResponse = $this->post(route('orders.complete', $order->access_token));
        $completeResponse->assertRedirect();
        $completeResponse->assertSessionHas('error');

        $order->refresh();
        $this->assertEquals('Siap diambil', $order->status);
    }

    public function test_customer_can_mark_ready_qris_order_as_completed(): void
    {
        $order = Order::where('payment_method', 'QRIS')->first();
        $order->update(['status' => 'Siap diambil', 'payment_status' => 'Lunas']);

        // Check that completion button is visible for non-cash orders
        $trackResponse = $this->get(route('orders.track', $order->access_token));
        $trackResponse->assertStatus(200);
        $trackResponse->assertSee('Pesanan Selesai / Sudah Diambil');

        // Customer clicks complete
        $completeResponse = $this->post(route('orders.complete', $order->access_token));
        $completeResponse->assertRedirect();
        $completeResponse->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('Selesai', $order->status);
    }
}
