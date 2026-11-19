<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCanteenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'seller_id' => ['required', 'exists:users,id', 'unique:canteens,seller_id'],
            'campus_location' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'opening_hours' => ['nullable', 'string', 'max:50'],
            'is_open' => ['boolean'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,webp,svg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kantin wajib diisi.',
            'seller_id.required' => 'Pilih akun penjual yang akan mengelola kantin ini.',
            'seller_id.unique' => 'Penjual yang dipilih sudah mengelola kantin lain. Aturan: 1 Seller hanya 1 Kantin.',
            'campus_location.required' => 'Lokasi kampus wajib diisi (contoh: Kampus Viktor Lt. 1).',
            'logo.max' => 'Ukuran logo maksimal 2 MB.',
        ];
    }
}
