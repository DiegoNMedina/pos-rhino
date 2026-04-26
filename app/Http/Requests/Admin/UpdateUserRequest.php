<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('manage-pos');
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $userId = is_object($user) ? $user->id : null;

        $allowedRoles = [UserRole::ADMIN, UserRole::SUPERVISOR, UserRole::CASHIER];
        if ($this->user() !== null && $this->user()->can('manage-platform')) {
            $allowedRoles[] = UserRole::SUPPORT;
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'role' => ['required', 'string', 'in:'.implode(',', $allowedRoles)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
