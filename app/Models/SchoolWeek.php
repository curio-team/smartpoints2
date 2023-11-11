<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolWeek extends Model
{
    use HasFactory;

    protected $fillable = [
        'year_start',
        'year_end',
        'week_number',
        'date_of_monday',
    ];

    protected $casts = [
        'date_of_monday' => 'date',
    ];

    /**
     * Find the closest week to now based on the monday of the week.
     */
    public static function getCurrentWeekNumber(): ?int
    {
        $now = now();
        $year = $now->year;

        $week = static::where('year_start', '<=', $year)
            ->where('year_end', '>=', $year)
            ->where('date_of_monday', '<=', $now->startOfWeek())
            ->orderBy('date_of_monday', 'desc')
            ->first();

        return $week?->week_number;
    }
}
