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
        Schema::create('teams', function (Blueprint $table) {
            $table->unsignedMediumInteger('id')->primary();
            $table->unsignedTinyInteger('conference_id')->nullable();
            $table->string('slug')->nullable();
            $table->string('location')->nullable();
            $table->string('name')->nullable();
            $table->string('nickname')->nullable();
            $table->string('abbreviation')->nullable();
            $table->string('display_name')->nullable();
            $table->string('short_display_name');
            $table->string('color')->nullable();
            $table->json('logos')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
