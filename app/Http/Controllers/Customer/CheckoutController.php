<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class CheckoutController extends Controller
{
    public function process(Request $request, OrderService $orderService): RedirectResponse
    {
        $validated = $request->validate([
            'nama_pengambil' => ['required', 'string', 'min:3', 'max:100'],
            'customer_type' => ['nullable', 'string', 'max:50'],
            'metode_pembayaran' => ['required', 'in:Tunai,QRIS'],
            'checkout_key' => ['required', 'string'],
        ], [
            'nama_pengambil.required' => 'Nama pemesan / pengambil wajib diisi.',
            'nama_pengambil.min' => 'Nama pemesan minimal 3 karakter.',
            'metode_pembayaran.required' => 'Pilih metode pembayaran (Tunai atau QRIS).',
            'checkout_key.required' => 'Kunci checkout sesi tidak valid. Silakan muat ulang keranjang.',
        ]);

        $cart = session()->get('cart', []);
        $canteenId = session()->get('cart_canteen_id');

        if (empty($cart) || !$canteenId) {
            return redirect()->route('customer.cart')->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        $canteen = Canteen::findOrFail($canteenId);

        try {
            $order = $orderService->createOrder(
                canteen: $canteen,
                customer: Auth::user(),
                cartItems: $cart,
                customerName: $validated['nama_pengambil'],
                customerType: $validated['customer_type'] ?? (Auth::user() ? ucfirst(Auth::user()->user_type ?? 'Civitas') : 'Umum'),
                paymentMethod: $validated['metode_pembayaran'],
                checkoutKey: $validated['checkout_key']
            );

            // Bersihkan sesi keranjang setelah sukses
            session()->forget(['cart', 'cart_canteen_id', 'checkout_key']);

            if ($order->payment_method === 'QRIS') {
                return redirect()->route('orders.qris', $order->access_token)
                    ->with('success', "Pesanan {$order->order_code} berhasil dibuat! Silakan lanjutkan simulasi pembayaran QRIS.");
            }

            return redirect()->route('orders.track', $order->access_token)
                ->with('success', "Pesanan {$order->order_code} berhasil dibuat! Pembayaran tunai dilakukan saat pengambilan di kantin.");
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem saat memproses checkout: ' . $e->getMessage());
        }
    }
}
