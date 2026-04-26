<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 50)->nullable()->after('email');
            $table->string('address', 255)->nullable()->after('phone');
            $table->string('city', 120)->nullable()->after('address');
            $table->string('state', 120)->nullable()->after('city');
            $table->string('country', 120)->nullable()->after('state');
            $table->string('postal_code', 20)->nullable()->after('country');
            $table->string('avatar_path', 255)->nullable()->after('postal_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'address',
                'city',
                'state',
                'country',
                'postal_code',
                'avatar_path',
            ]);
        });
    }
};
