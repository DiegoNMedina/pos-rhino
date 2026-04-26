<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $user = $request->user();
        $storeId = $user ? $user->store_id : null;

        $sales = Sale::query()
            ->with(['user', 'customer', 'branch', 'register'])
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
                        })
                        ->orWhereHas('customer', function ($customerQuery) use ($q) {
                            $customerQuery
                                ->where('name', 'like', '%'.$q.'%')
                                ->orWhere('phone', 'like', '%'.$q.'%')
                                ->orWhere('email', 'like', '%'.$q.'%');
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
        $sale->loadMissing(['customer']);

        return view('admin.sales.show', [
            'sale' => $sale,
        ]);
    }

    public function cancel(Request $request, Sale $sale)
    {
        $authUser = $request->user();
        $storeId = $authUser ? $authUser->store_id : null;
        if ($storeId !== null) {
            abort_unless((int) $sale->store_id === (int) $storeId, 404);
        }

        if ((string) $sale->status === Sale::STATUS_CANCELLED) {
            return back()->with('error', 'Esta venta ya fue cancelada.');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($sale, $storeId, $authUser, $validated, $request) {
            $sale->load('items');

            foreach ($sale->items as $item) {
                $product = Product::query()
                    ->whereKey($item->product_id)
                    ->when($storeId !== null, function ($builder) use ($storeId) {
                        $builder->where('store_id', $storeId);
                    })
                    ->lockForUpdate()
                    ->first();

                if (! $product || $product->stock === null) {
                    continue;
                }

                $product->stock = (float) $product->stock + (float) $item->quantity;
                $product->save();
            }

            $sale->status = Sale::STATUS_CANCELLED;
            $sale->cancelled_at = now();
            $sale->cancelled_by_user_id = $authUser ? $authUser->id : null;
            $sale->cancel_reason = (string) $validated['reason'];
            $sale->save();

            AuditLog::query()->create([
                'store_id' => $sale->store_id,
                'user_id' => $authUser ? $authUser->id : null,
                'action' => 'sale.cancelled',
                'subject_type' => 'sale',
                'subject_id' => $sale->id,
                'metadata' => [
                    'reason' => (string) $validated['reason'],
                ],
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255) ?: null,
            ]);
        });

        return back()->with('success', 'Venta cancelada y stock restaurado.');
    }
}
