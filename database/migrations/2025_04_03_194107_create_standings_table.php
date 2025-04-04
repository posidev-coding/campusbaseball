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
        Schema::create('standings', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('conference_id');
            $table->unsignedMediumInteger('team_id');
            $table->unsignedTinyInteger('ranking');
            $table->string('record', 5)->nullable();
            $table->json('stats')->nullable();
            $table->timestamps();
            $table->unique(['conference_id','team_id']);

    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standings');
    }
};
