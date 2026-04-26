<?php

namespace Tests\Feature;

use App\Enums\ProductUnitType;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Register;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosSaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_sale_and_decrements_inventory(): void
    {
        $store = Store::query()->create([
            'code' => 'TEST',
            'name' => 'Tienda Test',
            'is_active' => true,
            'plan' => 'starter',
            'subscription_status' => 'active',
            'billing_method' => 'transfer',
            'trial_ends_at' => null,
            'subscription_ends_at' => now()->addDays(30),
        ]);

        $branch = Branch::query()->create([
            'store_id' => $store->id,
            'name' => 'Matriz',
            'code' => 'MATRIZ',
            'is_active' => true,
        ]);

        $register = Register::query()->create([
            'store_id' => $store->id,
            'branch_id' => $branch->id,
            'name' => 'Caja 1',
            'code' => 'CAJA-1',
            'is_active' => true,
        ]);

        $user = User::factory()->create(['store_id' => $store->id]);

        $unitProduct = Product::query()->create([
            'store_id' => $store->id,
            'name' => 'Producto pieza',
            'code' => 'A1',
            'barcode' => '7500000000100',
            'unit_type' => ProductUnitType::UNIT,
            'price' => 10.00,
            'stock' => 10,
            'is_active' => true,
        ]);

        $weightProduct = Product::query()->create([
            'store_id' => $store->id,
            'name' => 'Producto peso',
            'code' => 'P1',
            'barcode' => '7500000000200',
            'unit_type' => ProductUnitType::WEIGHT,
            'price' => 20.00,
            'stock' => 10,
            'is_active' => true,
        ]);

        $res = $this
            ->actingAs($user)
            ->postJson('/pos/api/sales', [
                'branch_id' => $branch->id,
                'register_id' => $register->id,
                'payment_method' => 'cash',
                'cash_received' => 100,
                'items' => [
                    [
                        'product_id' => $unitProduct->id,
                        'quantity' => 2,
                        'unit_price' => 10.00,
                    ],
                    [
                        'product_id' => $weightProduct->id,
                        'quantity' => 1.5,
                        'unit_price' => 20.00,
                    ],
                ],
            ]);

        $res->assertCreated();
        $this->assertDatabaseCount('sales', 1);
        $this->assertDatabaseCount('sale_items', 2);

        $unitStock = (float) $unitProduct->fresh()->stock;
        $weightStock = (float) $weightProduct->fresh()->stock;

        $this->assertSame(8.0, $unitStock);
        $this->assertSame(8.5, $weightStock);
    }
}
