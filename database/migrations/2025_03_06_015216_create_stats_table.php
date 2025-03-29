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
        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('team_id');
            $table->enum('scope', ['overall', 'home', 'away']);
            $table->string('name');
            $table->string('display_name');
            $table->string('short_display_name');
            $table->string('description');
            $table->string('abbreviation');
            $table->decimal('stat_value', total: 5, places: 1);
            $table->string('display_value');
            $table->timestamps();

            $table->unique(['team_id', 'scope', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stats');
    }
};
