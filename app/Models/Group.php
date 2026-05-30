<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $name
 * @property int $group_id
 * @property int $cohort_id
 */
class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'cohort_id',
    ];
}
