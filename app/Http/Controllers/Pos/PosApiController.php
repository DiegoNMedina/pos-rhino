<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StoreSaleRequest;
use App\Repositories\ProductRepository;
use App\Services\Pos\PosService;
use Illuminate\Http\Request;

class PosApiController extends Controller
{
    public function searchProducts(Request $request, ProductRepository $products)
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:1', 'max:100'],
        ]);

        $user = $request->user();
        $storeId = $user ? $user->store_id : null;
        $results = $products->search($validated['q'], 20, $storeId)->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
                'barcode' => $product->barcode,
                'unit_type' => $product->unit_type,
                'price' => (float) $product->price,
                'stock' => $product->stock === null ? null : (float) $product->stock,
            ];
        });

        return response()->json([
            'data' => $results,
        ]);
    }

    public function storeSale(StoreSaleRequest $request, PosService $pos)
    {
        $validated = $request->validated();

        $user = $request->user();
        $storeId = $user ? $user->store_id : null;
        $sale = $pos->createSale(
            $storeId,
            (int) $validated['branch_id'],
            (int) $validated['register_id'],
            (int) $request->user()->id,
            $validated
        );

        return response()->json([
            'data' => [
                'id' => $sale->id,
                'total' => (float) $sale->total,
                'change_due' => $sale->change_due === null ? null : (float) $sale->change_due,
                'created_at' => $sale->created_at->toIso8601String(),
            ],
        ], 201);
    }
}
