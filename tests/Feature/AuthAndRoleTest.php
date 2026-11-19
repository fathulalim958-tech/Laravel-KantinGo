<?php

namespace Tests\Feature;

use App\Models\Canteen;
use App\Models\User;
use Tests\TestCase;

class AuthAndRoleTest extends TestCase
{
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('KantinGO');
    }

    public function test_admin_can_login_and_redirect_to_admin_dashboard(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->post('/login', [
            'login' => $admin->username,
            'password' => 'KantinGo123!',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_seller_can_login_and_redirect_to_seller_dashboard(): void
    {
        $seller = User::where('role', 'seller')->first();

        $response = $this->post('/login', [
            'login' => $seller->username,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('seller.dashboard'));
        $this->assertAuthenticatedAs($seller);
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = User::where('role', 'customer')->first();

        $response = $this->actingAs($customer)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_seller_cannot_access_admin_dashboard(): void
    {
        $seller = User::where('role', 'seller')->first();

        $response = $this->actingAs($seller)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_customer_can_register_successfully(): void
    {
        $email = 'test.mhs_' . uniqid() . '@mhs.unpam.ac.id';
        $username = 'testmhs_' . uniqid();

        $response = $this->post('/register', [
            'name' => 'Mahasiswa Baru UNPAM',
            'username' => $username,
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type' => 'mahasiswa',
            'identifier_number' => '221011400999',
            'phone' => '081234567899',
        ]);

        $response->assertRedirect(route('customer.canteens'));
        $this->assertDatabaseHas('users', [
            'username' => $username,
            'email' => $email,
            'role' => 'customer',
            'user_type' => 'mahasiswa',
        ]);
    }

    public function test_seller_a_cannot_manage_seller_b_canteen(): void
    {
        $sellerA = User::where('username', 'seller_viktor')->first();
        $canteenB = Canteen::where('slug', 'kantin-maju-jaya-witana')->first();

        $this->assertFalse($sellerA->can('update', $canteenB));
    }
}
