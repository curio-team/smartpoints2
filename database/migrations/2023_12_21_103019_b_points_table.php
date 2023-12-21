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
        Schema::create('b_points', function(Blueprint $table) {
            $table->id();
            $table->string('student_id')->references('id')->on('users');
            $table->string('teacher_id')->references('id')->on('users');
            $table->string('subject_id')->constrained();

            $table->integer('score')->nullable();
            $table->string('feedback')->nullable();

            $table->unique(['student_id', 'subject_id'], 'b_points_unique');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b_points');
    }
};
