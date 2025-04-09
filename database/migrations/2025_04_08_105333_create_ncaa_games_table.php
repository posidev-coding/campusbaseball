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
        Schema::create('ncaa_games', function (Blueprint $table) {

            $table->unsignedBigInteger('id')->primary();

            $table->date('game_date');
            $table->datetime('game_time');

            $table->string('game_state', 20);
            $table->string('status_code', 20);

            $table->unsignedMediumInteger('away_id');
            $table->unsignedMediumInteger('home_id');

            $table->boolean('boxscore_available')->default(false);
            $table->boolean('summary_available')->default(false);
            $table->boolean('pbp_available')->default(false);

            $table->json('linescores')->nullable();
            $table->json('stats')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ncaa_games');
    }
};
