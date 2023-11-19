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
        Schema::create('student_scores', function (Blueprint $table) {
            $table->id();

            // The student who received the score
            $table->string('student_id')->references('id')->on('users');

            // Which feedbackmoment is this score for?
            $table->unsignedBigInteger('feedbackmoment_id');

            // The score itself or if it's null the student is exempted
            $table->integer('score')->nullable();
            $table->string('feedback')->nullable();

            // The teacher who gave the score
            $table->string('teacher_id')->references('id')->on('users');

            // Was this a retake? If not, attempt = 1
            $table->unsignedInteger('attempt')->default(1);

            $table->timestamps();

            $table->unique(['student_id', 'feedbackmoment_id', 'attempt'], 'student_scores_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_scores');
    }
};
