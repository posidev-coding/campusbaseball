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
        Schema::create('ncaa_teams', function (Blueprint $table) {
            $table->unsignedMediumInteger('id')->primary();
            $table->string('slug', 25);
            $table->string('short_name', 50);
            $table->string('full_name', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ncaa_teams');
    }
};
