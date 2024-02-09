<?php

namespace App\Models;

use Illuminate\Support\Facades\Http;

class SchoolWeek
{
    public static function getCurrentWeekNumber(): ?int
    {
        return Http::get('https://week.curio.codes/api/only/week')->body();
    }
}
