<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private function rolesForAdminUi(?User $authUser): array
    {
        $roles = [
            UserRole::ADMIN => 'Admin',
            UserRole::SUPERVISOR => 'Supervisor',
            UserRole::CASHIER => 'Cajero',
        ];

        if ($authUser && $authUser->can('manage-platform')) {
            $roles[UserRole::SUPPORT] = 'Soporte';
        }

        return $roles;
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $authUser = $request->user();
        $storeId = $authUser ? $authUser->store_id : null;

        $users = User::query()
            ->when($storeId !== null, function ($builder) use ($storeId) {
                $builder->where('store_id', $storeId);
            })
            ->when($q !== '', function ($builder) use ($q) {
                $builder->where(function ($inner) use ($q) {
                    $inner
                        ->where('name', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%');
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'q' => $q,
            'roles' => $this->rolesForAdminUi($authUser),
        ]);
    }

    public function create()
    {
        $authUser = request()->user();
        if ($authUser && $authUser->store && ! $authUser->store->hasFeature('users')) {
            return redirect()->route('pricing')->with('error', 'Tu plan no incluye gestión de usuarios.');
        }

        return view('admin.users.create', [
            'roles' => $this->rolesForAdminUi($authUser),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $authUser = $request->user();
        if ($authUser && $authUser->store && ! $authUser->store->hasFeature('users')) {
            return redirect()->route('pricing')->with('error', 'Tu plan no incluye gestión de usuarios.');
        }

        $validated = $request->validated();

        if (($validated['role'] ?? null) === UserRole::SUPPORT) {
            $validated['store_id'] = null;
        }

        if ($authUser && $authUser->store_id !== null) {
            $validated['store_id'] = $authUser->store_id;
        }

        User::query()->create($validated);

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado.');
    }

    public function edit(User $user)
    {
        $authUser = request()->user();
        if ($authUser && $authUser->store_id !== null) {
            abort_unless((int) $user->store_id === (int) $authUser->store_id, 404);
        }

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => $this->rolesForAdminUi($authUser),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $authUser = $request->user();
        if ($authUser && $authUser->store_id !== null) {
            abort_unless((int) $user->store_id === (int) $authUser->store_id, 404);
        }

        $validated = $request->validated();

        if (! array_key_exists('password', $validated) || $validated['password'] === null || trim((string) $validated['password']) === '') {
            unset($validated['password']);
        }

        $user->fill($validated);
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado.');
    }
}
