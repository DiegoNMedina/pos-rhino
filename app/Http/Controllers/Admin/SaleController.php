<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $user = $request->user();
        $storeId = $user ? $user->store_id : null;

        $sales = Sale::query()
            ->with(['user', 'branch', 'register'])
            ->when($storeId !== null, function ($builder) use ($storeId) {
                $builder->where('store_id', $storeId);
            })
            ->when($q !== '', function ($builder) use ($q) {
                $builder->where(function ($inner) use ($q) {
                    $inner
                        ->where('id', $q)
                        ->orWhere('payment_method', 'like', '%'.$q.'%')
                        ->orWhereHas('user', function ($userQuery) use ($q) {
                            $userQuery->where('name', 'like', '%'.$q.'%')->orWhere('email', 'like', '%'.$q.'%');
                        });
                });
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.sales.index', [
            'sales' => $sales,
            'q' => $q,
        ]);
    }

    public function show(Sale $sale)
    {
        $user = request()->user();
        if ($user && $user->store_id !== null) {
            abort_unless((int) $sale->store_id === (int) $user->store_id, 404);
        }

        $sale->load(['items', 'user', 'branch', 'register']);

        return view('admin.sales.show', [
            'sale' => $sale,
        ]);
    }
}
