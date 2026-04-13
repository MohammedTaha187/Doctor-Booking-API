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
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('id')->primary()
                ->default(DB::raw('(UUID())'));
            $table->foreignUuid('patient_id')
                ->constrained(table: 'users')
                ->cascadeOnDelete();
            $table->foreignUuid('doctor_id')
                ->constrained(table: 'doctors')
                ->cascadeOnDelete();
            $table->foreignUuid('time_slot_id')
                ->constrained(table: 'time_slots')
                ->cascadeOnDelete();
            $table->foreignUuid('payment_id')->nullable();
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'])
                ->default('pending');
            $table->enum('type', ['online', 'in_person']);
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])
                ->default('unpaid');
            $table->string('meeting_link')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
