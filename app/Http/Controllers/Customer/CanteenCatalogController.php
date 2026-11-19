<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CanteenCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Canteen::with(['seller', 'menus' => function ($q) {
            $q->where('is_active', true)->where('stock', '>', 0);
        }])->latest();

        // Filter lokasi kampus
        if ($campus = $request->input('campus')) {
            $query->where('campus_location', 'like', "%{$campus}%");
        }

        // Pencarian nama kantin atau menu
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('campus_location', 'like', "%{$search}%")
                  ->orWhereHas('menus', function ($mq) use ($search) {
                      $mq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $canteens = $query->paginate(9)->withQueryString();

        // Hitung total kantin buka
        $openCount = Canteen::where('is_open', true)->count();

        return view('customer.canteens.index', compact('canteens', 'openCount'));
    }

    public function show(Canteen $canteen, Request $request): View
    {
        $canteen->load('seller');

        $menuQuery = $canteen->menus()->where('is_active', true);

        // Filter kategori
        if ($category = $request->input('kategori')) {
            if (in_array($category, ['Makanan', 'Minuman'], true)) {
                $menuQuery->where('kategori', $category);
            }
        }

        // Pencarian menu
        if ($search = $request->input('q')) {
            $menuQuery->where('name', 'like', "%{$search}%");
        }

        $menus = $menuQuery->orderBy('stock', 'desc')->orderBy('name')->get();

        // Ambil keranjang saat ini untuk menampilkan badge jumlah yang sudah dipilih
        $cart = session()->get('cart', []);
        $cartCanteenId = session()->get('cart_canteen_id');

        return view('customer.canteens.show', compact('canteen', 'menus', 'cart', 'cartCanteenId'));
    }
}
