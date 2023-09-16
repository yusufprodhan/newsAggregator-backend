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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('source_id')->nullable();
            $table->string('source_name')->nullable();
            $table->string('author')->nullable();
            $table->string('category');
            $table->text('content')->nullable();
            $table->text('description')->nullable();
            $table->text('url')->nullable();
            $table->longText('urlToImage')->nullable();
            $table->timestamp('publishedAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
