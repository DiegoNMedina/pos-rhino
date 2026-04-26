<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('manage-pos');
    }

    public function rules(): array
    {
        $product = $this->route('product');
        $productId = is_object($product) ? $product->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:100', Rule::unique('products', 'code')->ignore($productId)],
            'barcode' => ['nullable', 'string', 'max:100', Rule::unique('products', 'barcode')->ignore($productId)],
            'unit_type' => ['required', 'string', 'in:unit,weight'],
            'price' => ['required', 'numeric', 'gt:0'],
            'stock' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
