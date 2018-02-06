<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeeklyChallenge extends Model
{
    use SoftDeletes;

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

    protected $dates = [
        'deleted_at'
    ];
}
