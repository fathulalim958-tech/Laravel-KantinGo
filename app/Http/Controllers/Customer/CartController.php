<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $cart = session()->get('cart', []);
        $canteenId = session()->get('cart_canteen_id');

        $canteen = null;
        $cartItems = [];
        $totalPrice = 0;

        if ($canteenId && !empty($cart)) {
            $canteen = Canteen::find($canteenId);

            if ($canteen) {
                $menus = Menu::where('canteen_id', $canteen->id)
                    ->whereIn('id', array_keys($cart))
                    ->where('is_active', true)
                    ->get();

                foreach ($menus as $menu) {
                    $qty = (int)($cart[$menu->id] ?? 0);
                    if ($qty > 0) {
                        $subtotal = $menu->price * $qty;
                        $totalPrice += $subtotal;

                        $cartItems[] = [
                            'menu' => $menu,
                            'quantity' => $qty,
                            'subtotal' => $subtotal,
                        ];
                    }
                }
            }
        }

        // Generate atau pertahankan idempotency key untuk form checkout
        if (!session()->has('checkout_key')) {
            session()->put('checkout_key', Str::random(32));
        }
        $checkoutKey = session()->get('checkout_key');

        return view('customer.cart.index', compact('canteen', 'cartItems', 'totalPrice', 'checkoutKey'));
    }

    public function add(Request $request): RedirectResponse
    {
        $request->validate([
            'menu_id' => ['required', 'exists:menus,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'switch_canteen' => ['nullable', 'boolean'],
        ]);

        $menu = Menu::with('canteen')->findOrFail($request->input('menu_id'));

        if (!$menu->is_active || $menu->stock <= 0) {
            return back()->with('error', "Maaf, menu \"{$menu->name}\" saat ini sedang habis.");
        }

        $currentCanteenId = session()->get('cart_canteen_id');
        $cart = session()->get('cart', []);

        // Aturan Multi-Kantin: Satu checkout hanya boleh dari satu kantin
        if ($currentCanteenId && $currentCanteenId != $menu->canteen_id && !empty($cart)) {
            if (!$request->boolean('switch_canteen')) {
                $currentCanteen = Canteen::find($currentCanteenId);
                return back()->with('canteen_conflict', [
                    'current_canteen_name' => $currentCanteen->name ?? 'Kantin Lain',
                    'new_canteen_name' => $menu->canteen->name,
                    'new_menu_id' => $menu->id,
                    'new_menu_name' => $menu->name,
                ]);
            }

            // Jika pembeli menyetujui ganti kantin, kosongkan keranjang lama
            $cart = [];
        }

        $quantity = (int)$request->input('quantity', 1);
        $currentQty = (int)($cart[$menu->id] ?? 0);
        $newQty = $currentQty + $quantity;

        if ($newQty > $menu->stock) {
            return back()->with('error', "Stok \"{$menu->name}\" tersisa {$menu->stock} porsi. Anda sudah memiliki {$currentQty} di keranjang.");
        }

        $cart[$menu->id] = $newQty;

        session()->put('cart', $cart);
        session()->put('cart_canteen_id', $menu->canteen_id);

        return back()->with('success', "{$menu->name} berhasil ditambahkan ke keranjang!");
    }

    public function update(Request $request): RedirectResponse
    {
        $quantities = $request->input('qty', []);
        $cart = session()->get('cart', []);
        $canteenId = session()->get('cart_canteen_id');

        if (!$canteenId || empty($cart)) {
            return redirect()->route('customer.cart');
        }

        $menus = Menu::where('canteen_id', $canteenId)->whereIn('id', array_keys($quantities))->get()->keyBy('id');

        $updatedCart = [];
        foreach ($quantities as $menuId => $qty) {
            $qty = (int)$qty;
            if ($qty <= 0) continue; // Hapus jika 0

            $menu = $menus->get($menuId);
            if ($menu && $menu->is_active) {
                if ($qty > $menu->stock) {
                    return back()->with('error', "Stok {$menu->name} hanya tersisa {$menu->stock} porsi.");
                }
                $updatedCart[$menuId] = $qty;
            }
        }

        session()->put('cart', $updatedCart);

        if (empty($updatedCart)) {
            session()->forget(['cart', 'cart_canteen_id', 'checkout_key']);
            return redirect()->route('customer.cart')->with('info', 'Keranjang Anda telah dikosongkan.');
        }

        return redirect()->route('customer.cart')->with('success', 'Keranjang belanja berhasil diperbarui.');
    }

    public function clear(): RedirectResponse
    {
        session()->forget(['cart', 'cart_canteen_id', 'checkout_key']);
        return redirect()->route('customer.cart')->with('info', 'Keranjang belanja berhasil dikosongkan.');
    }
}
