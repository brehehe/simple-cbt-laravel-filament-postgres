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
        Schema::create('question_item_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('question_id');
            $table->foreignUuid('question_item_id');
            $table->string('title');
            $table->longText('description')->nullable();
            $table->decimal('weight')->default(0);
            $table->string('image')->nullable();
            $table->string('alphabet');
            $table->integer('order')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_item_answers');
    }
};
