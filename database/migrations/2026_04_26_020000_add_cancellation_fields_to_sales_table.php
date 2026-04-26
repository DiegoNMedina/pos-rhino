<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->timestamp('cancelled_at')->nullable()->after('change_due');
            $table->foreignId('cancelled_by_user_id')->nullable()->after('cancelled_at')->constrained('users')->nullOnDelete();
            $table->string('cancel_reason', 255)->nullable()->after('cancelled_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cancelled_by_user_id');
            $table->dropColumn(['cancelled_at', 'cancel_reason']);
        });
    }
};
