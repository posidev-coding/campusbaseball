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
        Schema::create('games', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->date('game_date');
            $table->datetime('game_time');
            $table->string('name');
            $table->string('short_name');

            $table->unsignedSmallInteger('season_id');
            $table->unsignedSmallInteger('season_type_id');

            $table->unsignedTinyInteger('status_id')->default(1);

            // Away Team
            $table->unsignedMediumInteger('away_id');
            $table->unsignedTinyInteger('away_rank')->default(0);
            $table->unsignedTinyInteger('away_runs')->default(0);
            $table->unsignedTinyInteger('away_hits')->default(0);
            $table->unsignedTinyInteger('away_errors')->default(0);
            $table->boolean('away_winner')->default(false);
            $table->json('away_box')->nullable();
            $table->json('away_stats')->nullable();
            $table->json('away_records')->nullable();
            $table->json('away_roster')->nullable();

            // Home Team
            $table->unsignedMediumInteger('home_id');
            $table->unsignedTinyInteger('home_rank')->default(0);
            $table->unsignedTinyInteger('home_runs')->default(0);
            $table->unsignedTinyInteger('home_hits')->default(0);
            $table->unsignedTinyInteger('home_errors')->default(0);
            $table->boolean('home_winner')->default(false);
            $table->json('home_box')->nullable();
            $table->json('home_stats')->nullable();
            $table->json('home_records')->nullable();
            $table->json('home_roster')->nullable();

            $table->unsignedBigInteger('play_page')->nullable()->default(1);
            $table->unsignedBigInteger('play_cursor')->nullable();
            $table->json('resources')->nullable();
            $table->json('status')->nullable();
            $table->json('venue')->nullable();
            $table->json('broadcasts')->nullable();
            $table->timestamps();

            $table->index('game_date');
            $table->index('play_cursor');
            $table->index('season_id');
            $table->index('season_type_id');
            $table->index('away_id');
            $table->index('home_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
