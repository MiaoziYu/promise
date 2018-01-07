<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Habit extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'credits',
        'count',
        'streak',
        'checked_at'
    ];
}
