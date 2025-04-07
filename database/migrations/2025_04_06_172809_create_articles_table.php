<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {

            $table->unsignedBigInteger('id')->primary();
            $table->string('article_type', 20);
            $table->string('link');
            $table->text('image', 1000)->nullable();
            $table->integer('game_id')->nullable()->unsigned();
            $table->string('headline');
            $table->text('description')->nullable();
            $table->longText('story')->nullable();
            $table->json('teams')->nullable();
            $table->json('story_images')->nullable();
            $table->json('story_videos')->nullable();
            $table->timestamp('published', 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}