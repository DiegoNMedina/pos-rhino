<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $storesCount = Store::query()->count();
        $activeStoresCount = Store::query()->where('subscription_status', 'active')->count();
        $usersCount = User::query()->count();

        $stores = Store::query()
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('platform.dashboard', [
            'storesCount' => $storesCount,
            'activeStoresCount' => $activeStoresCount,
            'usersCount' => $usersCount,
            'stores' => $stores,
        ]);
    }
}
