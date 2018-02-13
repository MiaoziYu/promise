<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Habit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'credits',
        'count',
        'streak',
        'max_streak',
        'checked_at',
        'order',
        'frozen',
    ];

    protected $dates = [
        'deleted_at'
    ];
}
