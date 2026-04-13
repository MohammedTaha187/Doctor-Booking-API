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
        Schema::create('locales', function (Blueprint $table) {
            $table->uuid('id')->primary()
                ->default(DB::raw('(UUID())'));
            $table->string('code', 5)->unique();
            $table->string('name');
            $table->string('native_name');
            $table->enum('direction', ['ltr', 'rtl'])
                ->default('ltr');
            $table->boolean('is_active')
                ->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locales');
    }
};
