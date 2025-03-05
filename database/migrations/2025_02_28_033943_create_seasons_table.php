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
        Schema::create('seasons', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->primary(); // year
            $table->string('name', 20);
            $table->string('description', 30);
            $table->unsignedTinyInteger('type_id');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->json('refs')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};
