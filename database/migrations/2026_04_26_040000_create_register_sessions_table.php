<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('register_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->constrained()->restrictOnDelete();
            $table->foreignId('register_id')->constrained()->restrictOnDelete();
            $table->foreignId('opened_by_user_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('opened_at')->useCurrent();
            $table->decimal('opening_cash', 10, 2)->default(0);
            $table->foreignId('closed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->decimal('closing_cash', 10, 2)->nullable();
            $table->decimal('expected_cash', 10, 2)->nullable();
            $table->decimal('difference', 10, 2)->nullable();
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->index(['store_id', 'register_id', 'opened_at']);
            $table->index(['register_id', 'closed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('register_sessions');
    }
};
