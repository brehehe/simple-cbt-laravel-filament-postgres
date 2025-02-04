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
        Schema::create('exam_student_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('exam_student_id');
            $table->foreignUuid('question_item_id');
            $table->foreignUuid('question_item_answer_id')->nullable();
            $table->enum('status',['belum','ragu-ragu','terpilih'])->default('belum');
            $table->integer('order')->default(0)->after('id'); // Menambahkan kolom order
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_student_details');
    }
};
