<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isSeller() && (bool)$this->user()->canteen;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'kategori' => ['required', 'in:Makanan,Minuman'],
            'price' => ['required', 'integer', 'min:500', 'max:10000000'],
            'stock' => ['required', 'integer', 'min:0', 'max:10000'],
            'description' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp,svg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama menu makanan/minuman wajib diisi.',
            'kategori.required' => 'Pilih kategori (Makanan atau Minuman).',
            'price.required' => 'Harga wajib diisi minimal Rp500.',
            'stock.required' => 'Stok porsi wajib diisi.',
            'image.max' => 'Ukuran foto maksimal 2 MB.',
        ];
    }
}
