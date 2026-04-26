<?php

namespace App\Services\Pos;

use App\Enums\PaymentMethod;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Register;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Repositories\ProductRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosService
{
    private ProductRepository $products;

    public function __construct(ProductRepository $products)
    {
        $this->products = $products;
    }

    public function createSale(?int $storeId, int $branchId, int $registerId, int $userId, array $payload): Sale
    {
        $paymentMethod = (string) Arr::get($payload, 'payment_method');
        $cashReceived = Arr::get($payload, 'cash_received');

        if (! in_array($paymentMethod, [PaymentMethod::CASH, PaymentMethod::CARD, PaymentMethod::MIXED], true)) {
            throw ValidationException::withMessages(['payment_method' => ['Método de pago inválido.']]);
        }

        $items = (array) Arr::get($payload, 'items', []);
        if (count($items) === 0) {
            throw ValidationException::withMessages(['items' => ['Agrega al menos un producto.']]);
        }

        return DB::transaction(function () use ($storeId, $branchId, $registerId, $userId, $items, $paymentMethod, $cashReceived) {
            $branch = Branch::query()
                ->whereKey($branchId)
                ->when($storeId !== null, function ($builder) use ($storeId) {
                    $builder->where('store_id', $storeId);
                })
                ->first();

            if (! $branch) {
                throw ValidationException::withMessages(['branch_id' => ['Sucursal inválida.']]);
            }

            $register = Register::query()
                ->whereKey($registerId)
                ->where('branch_id', $branchId)
                ->when($storeId !== null, function ($builder) use ($storeId) {
                    $builder->where('store_id', $storeId);
                })
                ->first();

            if (! $register) {
                throw ValidationException::withMessages(['register_id' => ['Caja inválida.']]);
            }

            $sale = new Sale;
            $sale->store_id = $storeId;
            $sale->branch_id = $branchId;
            $sale->register_id = $registerId;
            $sale->user_id = $userId;
            $sale->payment_method = $paymentMethod;
            $sale->status = Sale::STATUS_COMPLETED;
            $sale->subtotal = 0;
            $sale->discount_total = 0;
            $sale->tax_total = 0;
            $sale->total = 0;
            $sale->cash_received = $cashReceived === null ? null : (float) $cashReceived;
            $sale->change_due = null;
            $sale->save();

            $subtotal = 0.0;

            foreach ($items as $item) {
                $productId = (int) Arr::get($item, 'product_id');
                $quantity = (float) Arr::get($item, 'quantity');
                $unitPrice = Arr::get($item, 'unit_price');

                if ($productId <= 0) {
                    throw ValidationException::withMessages(['items' => ['Producto inválido.']]);
                }

                if ($quantity <= 0) {
                    throw ValidationException::withMessages(['items' => ['Cantidad inválida.']]);
                }

                $product = $this->products->findById($productId, $storeId);
                if (! $product) {
                    throw ValidationException::withMessages(['items' => ['Producto no encontrado.']]);
                }

                $resolvedUnitPrice = $unitPrice === null ? (float) $product->price : (float) $unitPrice;
                if ($resolvedUnitPrice <= 0) {
                    throw ValidationException::withMessages(['items' => ['Precio inválido.']]);
                }

                $lineTotal = round($quantity * $resolvedUnitPrice, 2);

                $saleItem = new SaleItem;
                $saleItem->sale_id = $sale->id;
                $saleItem->product_id = $product->id;
                $saleItem->name = $product->name;
                $saleItem->unit_type = $product->unit_type;
                $saleItem->quantity = $quantity;
                $saleItem->unit_price = $resolvedUnitPrice;
                $saleItem->total = $lineTotal;
                $saleItem->save();

                $subtotal += $lineTotal;

                $this->decrementInventory($product, $quantity);
            }

            $sale->subtotal = round($subtotal, 2);
            $sale->total = round($subtotal, 2);

            if ($paymentMethod === PaymentMethod::CASH || $paymentMethod === PaymentMethod::MIXED) {
                $received = (float) ($sale->cash_received ?? 0);
                $sale->change_due = round(max(0, $received - $sale->total), 2);
            }

            $sale->save();

            return $sale->load('items', 'user', 'branch', 'register');
        });
    }

    private function decrementInventory(Product $product, float $quantity): void
    {
        if ($product->stock === null) {
            return;
        }

        $newStock = (float) $product->stock - $quantity;
        $product->stock = max(0, $newStock);
        $product->save();
    }
}
