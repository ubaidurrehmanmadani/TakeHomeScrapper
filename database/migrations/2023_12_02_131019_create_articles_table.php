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
        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('api_source_id')->nullable();
            $table->string('source')->nullable();
            $table->string('author')->nullable();
            $table->mediumText('title')->nullable();
            $table->mediumText('description')->nullable();
            $table->string('url')->nullable();
            $table->string('published_at')->nullable();
            $table->json('article_json')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_api');
    }
};
