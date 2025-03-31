rea`1<?php

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
        Schema::create('plays', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('game_id');
            $table->unsignedMediumInteger('team_id');
            $table->unsignedBigInteger('atbat_id');
            $table->unsignedBigInteger('pitcher_id')->nullable();
            $table->unsignedBigInteger('batter_id')->nullable();
            $table->json('runners')->nullable();
            $table->unsignedMediumInteger('sequence');
            $table->unsignedMediumInteger('inning');
            $table->string('inning_type', 10);
            $table->string('inning_display', 25);
            $table->unsignedMediumInteger('type_id');
            $table->string('type_text', 50);
            $table->string('text');
            $table->boolean('scoring_play');
            $table->unsignedTinyInteger('outs')->default(0);
            $table->unsignedTinyInteger('score_value')->default(0);
            $table->unsignedTinyInteger('away_runs')->default(0);
            $table->unsignedTinyInteger('away_hits')->default(0);
            $table->unsignedTinyInteger('away_errors')->default(0);
            $table->unsignedTinyInteger('home_runs')->default(0);
            $table->unsignedTinyInteger('home_hits')->default(0);
            $table->unsignedTinyInteger('home_errors')->default(0);
            
            $table->timestamps();

            $table->index('game_id');
            $table->index('team_id');
            $table->index('pitcher_id');
            $table->index('batter_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plays');
    }
};
