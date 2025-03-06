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
        Schema::create('rankings', function (Blueprint $table) {
            $table->unsignedSmallInteger('season_id');
            $table->unsignedSmallInteger('season_type_id');
            $table->unsignedMediumInteger('team_id');
            $table->unsignedTinyInteger('week_nbr');
            $table->string('week_display');
            $table->string('headline');
            $table->unsignedTinyInteger('current');
            $table->unsignedTinyInteger('previous')->nullable();
            $table->string('trend')->nullable();
            $table->timestamps();

            $table->primary(['season_id', 'season_type_id', 'team_id', 'week_nbr']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rankings');
    }
};
