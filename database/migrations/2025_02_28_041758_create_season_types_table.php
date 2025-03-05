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
        Schema::create('season_types', function (Blueprint $table) {
            $table->unsignedSmallInteger('season_id');
            $table->unsignedSmallInteger('type_id');
            $table->string('name', 20);
            $table->string('slug', 30);
            $table->string('abbreviation', 10);
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->boolean('has_groups');
            $table->boolean('has_standings');
            $table->boolean('has_legs');
            $table->json('refs')->nullable();
            $table->timestamps();

            $table->index(['season_id', 'type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('season_types');
    }
};
