<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('manage-pos');
    }

    public function rules(): array
    {
        $allowedRoles = [UserRole::ADMIN, UserRole::SUPERVISOR, UserRole::CASHIER];
        if ($this->user() !== null && $this->user()->can('manage-platform')) {
            $allowedRoles[] = UserRole::SUPPORT;
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', 'in:'.implode(',', $allowedRoles)],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
