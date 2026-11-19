<?php

namespace Tests\Feature;

use App\Models\Canteen;
use App\Models\User;
use Tests\TestCase;

class AdminCanteenAndSellerTest extends TestCase
{
    protected function getAdmin(): User
    {
        return User::where('role', 'admin')->first();
    }

    public function test_admin_can_view_canteen_list(): void
    {
        $response = $this->actingAs($this->getAdmin())->get(route('admin.canteens.index'));
        $response->assertStatus(200);
        $response->assertSee('Kantin Utama Viktor UNPAM');
    }

    public function test_admin_can_create_new_seller(): void
    {
        $username = 'seller_baru_' . uniqid();
        $email = 'seller.' . uniqid() . '@kantin.unpam.ac.id';

        $response = $this->actingAs($this->getAdmin())->post(route('admin.sellers.store'), [
            'name' => 'Penjual Baru Viktor',
            'username' => $username,
            'email' => $email,
            'password' => 'password123',
            'phone' => '081234567899',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.sellers.index'));
        $this->assertDatabaseHas('users', [
            'username' => $username,
            'email' => $email,
            'role' => 'seller',
        ]);
    }

    public function test_admin_can_create_canteen_and_assign_unassigned_seller(): void
    {
        // Buat seller baru yang belum punya kantin
        $seller = User::create([
            'name' => 'Seller Baru Siap Kantin',
            'username' => 'seller_unassigned_' . uniqid(),
            'email' => 'unassigned.' . uniqid() . '@kantin.unpam.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'seller',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->getAdmin())->post(route('admin.canteens.store'), [
            'name' => 'Kantin Demo Baru',
            'seller_id' => $seller->id,
            'campus_location' => 'Kampus Viktor Lt. 2',
            'opening_hours' => '08:00 - 17:00 WIB',
            'is_open' => 1,
        ]);

        $response->assertRedirect(route('admin.canteens.index'));
        $this->assertDatabaseHas('canteens', [
            'name' => 'Kantin Demo Baru',
            'seller_id' => $seller->id,
        ]);
    }

    public function test_admin_cannot_assign_seller_who_already_manages_a_canteen(): void
    {
        // Ambil seller yang sudah punya kantin (misal seller_viktor)
        $existingCanteen = Canteen::first();
        $busySellerId = $existingCanteen->seller_id;

        $response = $this->actingAs($this->getAdmin())->post(route('admin.canteens.store'), [
            'name' => 'Kantin Duplikat Seller',
            'seller_id' => $busySellerId,
            'campus_location' => 'Kampus 1 Lt. 3',
        ]);

        $response->assertSessionHasErrors('seller_id');
    }

    public function test_admin_can_toggle_canteen_status(): void
    {
        $canteen = Canteen::first();
        $initialStatus = $canteen->is_open;

        $response = $this->actingAs($this->getAdmin())->post(route('admin.canteens.toggle-status', $canteen));
        $response->assertRedirect();

        $canteen->refresh();
        $this->assertEquals(!$initialStatus, $canteen->is_open);
    }
}
