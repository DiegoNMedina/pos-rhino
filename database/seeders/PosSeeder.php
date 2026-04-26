<?php

namespace Database\Seeders;

use App\Enums\ProductUnitType;
use App\Enums\UserRole;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Register;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hasStoreIdInBranches = Schema::hasColumn('branches', 'store_id');
        $hasStoreIdInRegisters = Schema::hasColumn('registers', 'store_id');
        $hasStoreIdInProducts = Schema::hasColumn('products', 'store_id');
        $hasStoreIdInUsers = Schema::hasColumn('users', 'store_id');

        $store = Store::query()->firstOrCreate(
            ['code' => 'DEMO'],
            [
                'name' => 'Tienda Demo',
                'is_active' => true,
                'plan' => 'pro',
                'subscription_status' => 'active',
                'billing_method' => 'transfer',
                'trial_ends_at' => null,
                'subscription_ends_at' => now()->addDays(30),
            ]
        );

        $branches = [
            Branch::query()->firstOrCreate(
                ['code' => 'MATRIZ'],
                array_filter([
                    'name' => 'Matriz',
                    'is_active' => true,
                    'store_id' => $hasStoreIdInBranches ? $store->id : null,
                ], fn ($v) => $v !== null)
            ),
            Branch::query()->firstOrCreate(
                ['code' => 'SUC-01'],
                array_filter([
                    'name' => 'Sucursal 01',
                    'is_active' => true,
                    'store_id' => $hasStoreIdInBranches ? $store->id : null,
                ], fn ($v) => $v !== null)
            ),
        ];

        foreach ($branches as $branch) {
            Register::query()->firstOrCreate(
                ['code' => $branch->code.'-CAJA-1'],
                array_filter([
                    'branch_id' => $branch->id,
                    'name' => 'Caja 1',
                    'is_active' => true,
                    'store_id' => $hasStoreIdInRegisters ? $store->id : null,
                ], fn ($v) => $v !== null)
            );

            Register::query()->firstOrCreate(
                ['code' => $branch->code.'-CAJA-2'],
                array_filter([
                    'branch_id' => $branch->id,
                    'name' => 'Caja 2',
                    'is_active' => true,
                    'store_id' => $hasStoreIdInRegisters ? $store->id : null,
                ], fn ($v) => $v !== null)
            );
        }

        User::query()->firstOrCreate(
            ['email' => 'superadmin@pos.test'],
            array_filter([
                'name' => 'Super Admin',
                'password' => 'password',
                'role' => UserRole::SUPER_ADMIN,
                'store_id' => null,
            ], fn ($v) => $v !== null)
        );

        User::query()->firstOrCreate(
            ['email' => 'admin@pos.test'],
            array_filter([
                'name' => 'Admin',
                'password' => 'password',
                'role' => UserRole::ADMIN,
                'store_id' => $hasStoreIdInUsers ? $store->id : null,
            ], fn ($v) => $v !== null)
        );

        User::query()->firstOrCreate(
            ['email' => 'supervisor@pos.test'],
            array_filter([
                'name' => 'Supervisor',
                'password' => 'password',
                'role' => UserRole::SUPERVISOR,
                'store_id' => $hasStoreIdInUsers ? $store->id : null,
            ], fn ($v) => $v !== null)
        );

        User::query()->firstOrCreate(
            ['email' => 'cajero1@pos.test'],
            array_filter([
                'name' => 'Cajero 1',
                'password' => 'password',
                'role' => UserRole::CASHIER,
                'store_id' => $hasStoreIdInUsers ? $store->id : null,
            ], fn ($v) => $v !== null)
        );

        User::query()->firstOrCreate(
            ['email' => 'cajero2@pos.test'],
            array_filter([
                'name' => 'Cajero 2',
                'password' => 'password',
                'role' => UserRole::CASHIER,
                'store_id' => $hasStoreIdInUsers ? $store->id : null,
            ], fn ($v) => $v !== null)
        );

        $products = [
            [
                'name' => 'Coca Cola 600ml',
                'code' => 'A0001',
                'barcode' => '7500000000001',
                'unit_type' => ProductUnitType::UNIT,
                'price' => 18.00,
                'stock' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'Tortilla (kg)',
                'code' => 'P0001',
                'barcode' => '7500000000002',
                'unit_type' => ProductUnitType::WEIGHT,
                'price' => 22.50,
                'stock' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Manzana (kg)',
                'code' => 'P0002',
                'barcode' => '7500000000003',
                'unit_type' => ProductUnitType::WEIGHT,
                'price' => 49.90,
                'stock' => 80,
                'is_active' => true,
            ],
            [
                'name' => 'Pan blanco',
                'code' => 'A0002',
                'barcode' => '7500000000004',
                'unit_type' => ProductUnitType::UNIT,
                'price' => 39.00,
                'stock' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'Leche 1L',
                'code' => 'A0003',
                'barcode' => '7500000000005',
                'unit_type' => ProductUnitType::UNIT,
                'price' => 27.50,
                'stock' => 24,
                'is_active' => true,
            ],
            [
                'name' => 'Huevo (docena)',
                'code' => 'A0004',
                'barcode' => '7500000000006',
                'unit_type' => ProductUnitType::UNIT,
                'price' => 62.00,
                'stock' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Arroz 1kg',
                'code' => 'A0005',
                'barcode' => '7500000000007',
                'unit_type' => ProductUnitType::UNIT,
                'price' => 31.90,
                'stock' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Frijol 1kg',
                'code' => 'A0006',
                'barcode' => '7500000000008',
                'unit_type' => ProductUnitType::UNIT,
                'price' => 44.50,
                'stock' => 18,
                'is_active' => true,
            ],
            [
                'name' => 'Azúcar 1kg',
                'code' => 'A0007',
                'barcode' => '7500000000009',
                'unit_type' => ProductUnitType::UNIT,
                'price' => 33.00,
                'stock' => 12,
                'is_active' => true,
            ],
            [
                'name' => 'Sal 1kg',
                'code' => 'A0008',
                'barcode' => '7500000000010',
                'unit_type' => ProductUnitType::UNIT,
                'price' => 16.00,
                'stock' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Aceite 1L',
                'code' => 'A0009',
                'barcode' => '7500000000011',
                'unit_type' => ProductUnitType::UNIT,
                'price' => 59.00,
                'stock' => 14,
                'is_active' => true,
            ],
            [
                'name' => 'Atún lata',
                'code' => 'A0010',
                'barcode' => '7500000000012',
                'unit_type' => ProductUnitType::UNIT,
                'price' => 23.90,
                'stock' => 40,
                'is_active' => true,
            ],
            [
                'name' => 'Jabón de baño',
                'code' => 'A0011',
                'barcode' => '7500000000013',
                'unit_type' => ProductUnitType::UNIT,
                'price' => 14.50,
                'stock' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Papel higiénico (4)',
                'code' => 'A0012',
                'barcode' => '7500000000014',
                'unit_type' => ProductUnitType::UNIT,
                'price' => 42.00,
                'stock' => 25,
                'is_active' => true,
            ],
            [
                'name' => 'Pollo (kg)',
                'code' => 'P0003',
                'barcode' => '7500000000015',
                'unit_type' => ProductUnitType::WEIGHT,
                'price' => 89.90,
                'stock' => 40,
                'is_active' => true,
            ],
            [
                'name' => 'Carne molida (kg)',
                'code' => 'P0004',
                'barcode' => '7500000000016',
                'unit_type' => ProductUnitType::WEIGHT,
                'price' => 139.00,
                'stock' => 25,
                'is_active' => true,
            ],
            [
                'name' => 'Queso Oaxaca (kg)',
                'code' => 'P0005',
                'barcode' => '7500000000017',
                'unit_type' => ProductUnitType::WEIGHT,
                'price' => 169.00,
                'stock' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Plátano (kg)',
                'code' => 'P0006',
                'barcode' => '7500000000018',
                'unit_type' => ProductUnitType::WEIGHT,
                'price' => 28.90,
                'stock' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'Jitomate (kg)',
                'code' => 'P0007',
                'barcode' => '7500000000019',
                'unit_type' => ProductUnitType::WEIGHT,
                'price' => 34.50,
                'stock' => 35,
                'is_active' => true,
            ],
            [
                'name' => 'Cebolla (kg)',
                'code' => 'P0008',
                'barcode' => '7500000000020',
                'unit_type' => ProductUnitType::WEIGHT,
                'price' => 29.90,
                'stock' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Chile serrano (kg)',
                'code' => 'P0009',
                'barcode' => '7500000000021',
                'unit_type' => ProductUnitType::WEIGHT,
                'price' => 69.90,
                'stock' => 12,
                'is_active' => true,
            ],
            [
                'name' => 'Limón (kg)',
                'code' => 'P0010',
                'barcode' => '7500000000022',
                'unit_type' => ProductUnitType::WEIGHT,
                'price' => 39.90,
                'stock' => 22,
                'is_active' => true,
            ],
            [
                'name' => 'Galletas',
                'code' => 'A0013',
                'barcode' => '7500000000023',
                'unit_type' => ProductUnitType::UNIT,
                'price' => 18.00,
                'stock' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Agua 1.5L',
                'code' => 'A0014',
                'barcode' => '7500000000024',
                'unit_type' => ProductUnitType::UNIT,
                'price' => 16.00,
                'stock' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Producto descontinuado',
                'code' => 'A0099',
                'barcode' => '7500000000099',
                'unit_type' => ProductUnitType::UNIT,
                'price' => 1.00,
                'stock' => 0,
                'is_active' => false,
            ],
        ];

        foreach ($products as $product) {
            if ($hasStoreIdInProducts) {
                $product['store_id'] = $store->id;
            }
            Product::query()->updateOrCreate(
                ['code' => $product['code']],
                $product
            );
        }
    }
}
