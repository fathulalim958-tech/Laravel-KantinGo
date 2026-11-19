<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Canteen;
use App\Models\Order;
use App\Models\User;
use Tests\TestCase;

class AdminMonitoringTest extends TestCase
{
    protected function getAdmin(): User
    {
        return User::where('role', 'admin')->first();
    }

    protected function getSeller(): User
    {
        return User::where('role', 'seller')->first();
    }

    protected function getCustomer(): User
    {
        return User::where('role', 'customer')->first();
    }

    public function test_admin_can_access_dashboard_and_see_platform_stats(): void
    {
        $response = $this->actingAs($this->getAdmin())->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Pusat Monitoring Platform KantinGO');
        $response->assertSee('Total Omzet Platform');
        $response->assertSee('Total Transaksi');
        $response->assertSee('Stan Kantin Aktif');
        $response->assertSee('Civitas Terdaftar');
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $responseSeller = $this->actingAs($this->getSeller())->get(route('admin.dashboard'));
        $responseSeller->assertStatus(403);

        $responseCustomer = $this->actingAs($this->getCustomer())->get(route('admin.dashboard'));
        $responseCustomer->assertStatus(403);
    }

    public function test_admin_can_view_customers_list_and_filter(): void
    {
        $response = $this->actingAs($this->getAdmin())->get(route('admin.customers.index', [
            'user_type' => 'mahasiswa',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Akun Civitas Kampus');
        $response->assertSee('budi_mhs');
    }

    public function test_admin_can_view_customer_details_and_orders(): void
    {
        $customer = $this->getCustomer();

        $response = $this->actingAs($this->getAdmin())->get(route('admin.customers.show', $customer));

        $response->assertStatus(200);
        $response->assertSee($customer->name);
        $response->assertSee($customer->email);
        $response->assertSee('Riwayat Pesanan Pelanggan');
    }

    public function test_admin_can_update_customer_and_toggle_status(): void
    {
        $customer = User::create([
            'name' => 'Testing Mhs Temp',
            'username' => 'mhs_temp_' . uniqid(),
            'email' => 'mhs_temp_' . uniqid() . '@unpam.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'user_type' => 'mahasiswa',
            'identifier_number' => '2021149999',
            'is_active' => true,
        ]);

        // Toggle Status (deactivate)
        $toggleResponse = $this->actingAs($this->getAdmin())
            ->post(route('admin.customers.toggle-status', $customer));
        $toggleResponse->assertRedirect();

        $customer->refresh();
        $this->assertFalse((bool)$customer->is_active);

        // Update data
        $updateResponse = $this->actingAs($this->getAdmin())
            ->put(route('admin.customers.update', $customer), [
                'name' => 'Testing Mhs Updated',
                'username' => $customer->username,
                'email' => $customer->email,
                'user_type' => 'dosen',
                'identifier_number' => '0412345678',
                'is_active' => 1,
            ]);

        $updateResponse->assertRedirect(route('admin.customers.index'));

        $customer->refresh();
        $this->assertEquals('Testing Mhs Updated', $customer->name);
        $this->assertEquals('dosen', $customer->user_type);
        $this->assertTrue((bool)$customer->is_active);
    }

    public function test_admin_can_monitor_all_canteen_transactions_and_view_order_detail(): void
    {
        $order = Order::latest()->first();

        // Index page
        $indexResponse = $this->actingAs($this->getAdmin())->get(route('admin.transactions.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Monitoring Transaksi Multi-Kantin');
        $indexResponse->assertSee($order->order_code);

        // Show page
        $showResponse = $this->actingAs($this->getAdmin())->get(route('admin.transactions.show', $order));
        $showResponse->assertStatus(200);
        $showResponse->assertSee($order->order_code);
        $showResponse->assertSee('Rincian Menu Dipesan');
        $showResponse->assertSee('Snapshot');
    }

    public function test_admin_can_view_activity_logs_and_filter(): void
    {
        ActivityLog::create([
            'user_id' => $this->getAdmin()->id,
            'action' => 'TEST_AUDIT_LOG',
            'description' => 'Aktivitas audit uji coba untuk unit test',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->getAdmin())->get(route('admin.activity-logs.index', [
            'action' => 'TEST_AUDIT_LOG',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Audit Trail & Log Aktivitas');
        $response->assertSee('TEST_AUDIT_LOG');
        $response->assertSee('Aktivitas audit uji coba untuk unit test');
    }
}
