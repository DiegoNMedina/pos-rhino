<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $user = $request->user();
        $storeId = $user ? $user->store_id : null;

        $products = Product::query()
            ->when($storeId !== null, function ($builder) use ($storeId) {
                $builder->where('store_id', $storeId);
            })
            ->when($q !== '', function ($builder) use ($q) {
                $builder->where(function ($inner) use ($q) {
                    $inner
                        ->where('name', 'like', '%'.$q.'%')
                        ->orWhere('code', $q)
                        ->orWhere('barcode', $q);
                });
            })
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'q' => $q,
        ]);
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(StoreProductRequest $request)
    {
        $user = $request->user();
        $payload = $request->validated();
        if ($user && $user->store_id !== null) {
            $payload['store_id'] = $user->store_id;
        }

        Product::query()->create($payload);

        return redirect()->route('admin.products.index')->with('success', 'Producto creado.');
    }

    public function edit(Product $product)
    {
        $user = request()->user();
        if ($user && $user->store_id !== null) {
            abort_unless((int) $product->store_id === (int) $user->store_id, 404);
        }

        return view('admin.products.edit', [
            'product' => $product,
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $user = $request->user();
        if ($user && $user->store_id !== null) {
            abort_unless((int) $product->store_id === (int) $user->store_id, 404);
        }

        $product->fill($request->validated());
        $product->save();

        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Product $product)
    {
        $user = request()->user();
        if ($user && $user->store_id !== null) {
            abort_unless((int) $product->store_id === (int) $user->store_id, 404);
        }

        $product->is_active = false;
        $product->save();

        return redirect()->route('admin.products.index')->with('success', 'Producto desactivado.');
    }
}
