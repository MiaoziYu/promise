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
        'checked_at',
        'order'
    ];

    protected $dates = [
        'deleted_at'
    ];
}
