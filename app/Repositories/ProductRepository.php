<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    public function search(string $query, int $limit = 20, ?int $storeId = null): Collection
    {
        $query = trim($query);

        if ($query === '') {
            return collect();
        }

        return Product::query()
            ->where('is_active', true)
            ->when($storeId !== null, function ($builder) use ($storeId) {
                $builder->where('store_id', $storeId);
            })
            ->where(function ($builder) use ($query) {
                $builder
                    ->where('code', $query)
                    ->orWhere('barcode', $query)
                    ->orWhere('name', 'like', '%'.$query.'%');
            })
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    public function findById(int $id, ?int $storeId = null): ?Product
    {
        return Product::query()
            ->whereKey($id)
            ->where('is_active', true)
            ->when($storeId !== null, function ($builder) use ($storeId) {
                $builder->where('store_id', $storeId);
            })
            ->first();
    }
}
