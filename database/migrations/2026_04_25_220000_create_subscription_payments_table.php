<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->string('provider')->default('stripe')->index();
            $table->string('event_type')->nullable()->index();
            $table->string('reference_id')->nullable()->index();
            $table->string('status')->nullable()->index();
            $table->string('currency', 10)->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->timestamp('period_start_at')->nullable();
            $table->timestamp('period_end_at')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
