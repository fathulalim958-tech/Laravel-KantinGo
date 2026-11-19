<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCanteenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $canteenId = $this->route('canteen')->id ?? $this->route('canteen');

        return [
            'name' => ['required', 'string', 'max:100'],
            'seller_id' => [
                'required',
                'exists:users,id',
                Rule::unique('canteens', 'seller_id')->ignore($canteenId),
            ],
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
            'seller_id.required' => 'Pilih penjual pengelola kantin.',
            'seller_id.unique' => 'Penjual ini sudah ditugaskan ke kantin lain.',
            'campus_location.required' => 'Lokasi kampus wajib diisi.',
        ];
    }
}
