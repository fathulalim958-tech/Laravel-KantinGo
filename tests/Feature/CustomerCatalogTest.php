<?php

namespace Tests\Feature;

use App\Models\Canteen;
use App\Models\User;
use Tests\TestCase;

class CustomerCatalogTest extends TestCase
{
    protected function getCustomer(): User
    {
        return User::where('role', 'customer')->first();
    }

    public function test_customer_can_view_canteens_marketplace(): void
    {
        $response = $this->actingAs($this->getCustomer())->get(route('customer.canteens'));
        $response->assertStatus(200);
        $firstCanteen = Canteen::active()->latest()->first();
        $response->assertSee($firstCanteen->name);
        $response->assertSee('Universitas Pamulang');
    }

    public function test_customer_can_filter_canteens_by_campus(): void
    {
        $response = $this->actingAs($this->getCustomer())->get(route('customer.canteens', ['campus' => 'Viktor']));
        $response->assertStatus(200);
        $response->assertSee('Kantin Utama Viktor UNPAM');
    }

    public function test_customer_can_view_canteen_detail_and_menus(): void
    {
        $canteen = Canteen::where('slug', 'kantin-utama-viktor')->first();

        $response = $this->actingAs($this->getCustomer())->get(route('customer.canteens.show', $canteen));
        $response->assertStatus(200);
        $response->assertSee($canteen->name);
        $response->assertSee('Nasi ayam');
        $response->assertSee('Rp12.000');
    }

    public function test_customer_can_filter_canteen_menus_by_category(): void
    {
        $canteen = Canteen::where('slug', 'kantin-utama-viktor')->first();

        $response = $this->actingAs($this->getCustomer())->get(route('customer.canteens.show', [
            'canteen' => $canteen,
            'kategori' => 'Minuman',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Es teh');
        $response->assertSee('Es jeruk');
    }

    public function test_guest_cannot_access_customer_catalog(): void
    {
        $response = $this->get(route('customer.canteens'));
        $response->assertRedirect(route('login'));
    }
}
