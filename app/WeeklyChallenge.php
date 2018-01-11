<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WeeklyChallenge extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'credits',
        'goal',
        'count',
        'failed',
        'order',
    ];
}
