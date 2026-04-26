<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Register;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSalesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_sale_detail(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@pos.test')->firstOrFail();
        $store = Store::query()->whereKey($admin->store_id)->firstOrFail();
        $branch = Branch::query()->firstOrFail();
        $register = Register::query()->firstOrFail();
        $product = Product::query()->firstOrCreate([
            'name' => 'Producto test',
            'code' => 'TEST-001',
            'barcode' => null,
            'unit_type' => 'unit',
            'price' => 10,
            'stock' => null,
            'is_active' => true,
            'store_id' => $store->id,
        ]);

        $sale = Sale::query()->create([
            'store_id' => $store->id,
            'branch_id' => $branch->id,
            'register_id' => $register->id,
            'user_id' => $admin->id,
            'status' => Sale::STATUS_COMPLETED,
            'payment_method' => 'cash',
            'subtotal' => 10,
            'discount_total' => 0,
            'tax_total' => 0,
            'total' => 10,
            'cash_received' => 10,
            'change_due' => 0,
        ]);

        SaleItem::query()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'name' => 'Prueba',
            'unit_type' => 'unit',
            'quantity' => 1,
            'unit_price' => 10,
            'total' => 10,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.sales.show', $sale))
            ->assertOk()
            ->assertSee('Venta #'.$sale->id);
    }
}
