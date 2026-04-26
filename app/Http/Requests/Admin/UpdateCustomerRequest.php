<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('manage-pos');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
