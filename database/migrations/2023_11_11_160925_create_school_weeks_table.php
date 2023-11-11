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
        Schema::create('school_weeks', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('year_start');
            $table->unsignedInteger('year_end');

            $table->unsignedInteger('week_number');
            $table->date('date_of_monday');

            $table->unique(['year_start', 'year_end', 'week_number'], 'unique_school_year_week_number');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_weeks');
    }
};
