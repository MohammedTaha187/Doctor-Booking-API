<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary()
                ->default(DB::raw('(UUID())'));
            $table->foreignUuid('user_id')
                ->constrained(table: 'users')
                ->cascadeOnDelete();
            $table->foreignUuid('appointment_id')
                ->nullable()
                ->constrained(table: 'appointments')
                ->nullOnDelete();
            $table->enum('gateway', ['paymob', 'stripe', 'paypal']);
            $table->string('gateway_transaction_id')->nullable();
            $table->string('gateway_order_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('EGP');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])
                ->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->foreign('payment_id')
                ->references('id')
                ->on('payments')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['payment_id']);
        });

        Schema::dropIfExists('payments');
    }
};
