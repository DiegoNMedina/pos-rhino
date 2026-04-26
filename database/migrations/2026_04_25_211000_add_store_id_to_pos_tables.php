<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->ensureStoreId('branches', 'branches_store_id_fk');
        $this->ensureStoreId('registers', 'registers_store_id_fk');
        $this->ensureStoreId('products', 'products_store_id_fk');
        $this->ensureStoreId('sales', 'sales_store_id_fk');

        $storeId = DB::table('stores')->orderBy('id')->value('id');
        if ($storeId === null) {
            $storeId = DB::table('stores')->insertGetId([
                'code' => 'DEFAULT',
                'name' => 'Tienda',
                'is_active' => 1,
                'plan' => 'starter',
                'subscription_status' => 'active',
                'billing_method' => 'transfer',
                'trial_ends_at' => null,
                'subscription_ends_at' => now(),
                'stripe_customer_id' => null,
                'stripe_subscription_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('branches')->whereNull('store_id')->update(['store_id' => $storeId]);
        DB::table('registers')->whereNull('store_id')->update(['store_id' => $storeId]);
        DB::table('products')->whereNull('store_id')->update(['store_id' => $storeId]);
        DB::table('sales')->whereNull('store_id')->update(['store_id' => $storeId]);
    }

    public function down(): void
    {
        $this->dropStoreId('sales', 'sales_store_id_fk');
        $this->dropStoreId('products', 'products_store_id_fk');
        $this->dropStoreId('registers', 'registers_store_id_fk');
        $this->dropStoreId('branches', 'branches_store_id_fk');
    }

    private function ensureStoreId(string $tableName, string $fkName): void
    {
        if (! Schema::hasColumn($tableName, 'store_id')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedBigInteger('store_id')->nullable()->index();
            });
        }

        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        if ($this->foreignKeyExists($tableName, 'store_id')) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($fkName) {
            $table->foreign('store_id', $fkName)->references('id')->on('stores')->nullOnDelete();
        });
    }

    private function dropStoreId(string $tableName, string $fkName): void
    {
        if (! Schema::hasColumn($tableName, 'store_id')) {
            return;
        }

        if (DB::getDriverName() === 'mysql' && $this->foreignKeyExists($tableName, 'store_id')) {
            Schema::table($tableName, function (Blueprint $table) use ($fkName) {
                $table->dropForeign($fkName);
            });
        }

        Schema::table($tableName, function (Blueprint $table) {
            $table->dropColumn('store_id');
        });
    }

    private function foreignKeyExists(string $tableName, string $columnName): bool
    {
        if (DB::getDriverName() !== 'mysql') {
            return false;
        }

        $databaseName = DB::connection()->getDatabaseName();

        return DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $databaseName)
            ->where('TABLE_NAME', $tableName)
            ->where('COLUMN_NAME', $columnName)
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->exists();
    }
};
