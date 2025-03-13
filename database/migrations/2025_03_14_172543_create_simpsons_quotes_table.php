<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('simpsons_quotes', function (Blueprint $table) {
            $table->id();
            $table->text('quote');
            $table->text('character');
            $table->text('image');
            $table->text('characterDirection');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simpsons_quotes');
    }
};
