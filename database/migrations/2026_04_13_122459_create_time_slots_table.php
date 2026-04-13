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
        Schema::create('time_slots', function (Blueprint $table) {
            $table->uuid('id')->primary()
                ->default(DB::raw('(UUID())'));
            $table->foreignUuid('doctor_id')
                ->constrained()->cascadeOnDelete();
            $table->enum(
                'day_of_week',
                ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday']
            );
            $table->time('start_time');
            $table->time('end_time');
            $table->tinyInteger('duration_minutes')
                ->default(30);
            $table->boolean('is_available')
                ->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};
