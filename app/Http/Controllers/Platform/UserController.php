<?php

namespace App\Http\Controllers\Platform;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $users = User::query()
            ->with('store')
            ->when($q !== '', function ($builder) use ($q) {
                $builder->where(function ($inner) use ($q) {
                    $inner
                        ->where('name', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%')
                        ->orWhere('role', 'like', '%'.$q.'%');
                });
            })
            ->orderBy('id')
            ->paginate(25)
            ->withQueryString();

        return view('platform.users.index', [
            'users' => $users,
            'q' => $q,
        ]);
    }

    public function storeSupport(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::SUPPORT,
            'store_id' => null,
        ]);

        return back()->with('success', 'Usuario de soporte creado.');
    }
}
