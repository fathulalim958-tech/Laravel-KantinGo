<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        $menu = $this->route('menu');
        return $this->user() && $this->user()->can('update', $menu);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'kategori' => ['required', 'in:Makanan,Minuman'],
            'price' => ['required', 'integer', 'min:500', 'max:10000000'],
            'stock' => ['required', 'integer', 'min:0', 'max:10000'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp,svg', 'max:2048'],
        ];
    }
}
