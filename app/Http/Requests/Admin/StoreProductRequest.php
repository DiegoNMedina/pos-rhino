<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('manage-pos');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:100', 'unique:products,code'],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:products,barcode'],
            'unit_type' => ['required', 'string', 'in:unit,weight'],
            'price' => ['required', 'numeric', 'gt:0'],
            'stock' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
