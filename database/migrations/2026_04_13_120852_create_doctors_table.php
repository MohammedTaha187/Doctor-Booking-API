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
        Schema::create('doctors', function (Blueprint $table) {
            $table->uuid('id')->primary()
                ->default(DB::raw('(UUID())'));
            $table->foreignUuid('user_id')
                ->constrained()->cascadeOnDelete();
            $table->foreignUuid('specialty_id')
                ->constrained()->cascadeOnDelete();
            $table->string('license_number')->unique();
            $table->tinyInteger('years_experience')->unsigned();
            $table->decimal('consultation_fee', 8, 2)->nullable();
            $table->enum('consultation_type', ['online', 'in_person', 'both']);
            $table->decimal('rating', 3, 2)
                ->default(0.00);
            $table->unsignedInteger('reviews_count')
                ->default(0);
            $table->boolean('is_verified')
                ->default(false);
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
        Schema::dropIfExists('doctors');
    }
};
