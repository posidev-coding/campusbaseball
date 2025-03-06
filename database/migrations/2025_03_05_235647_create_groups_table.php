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
        Schema::create('groups', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->primary();
            $table->string('name')->nullable();
            $table->string('abbreviation')->nullable();
            $table->string('short_name')->nullable();
            $table->string('midsize_name')->nullable();
            $table->boolean('is_conference')->default(false);
            $table->unsignedSmallInteger('parent_id')->nullable();
            $table->timestamps();

            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
