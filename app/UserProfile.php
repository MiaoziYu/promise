<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'credits',
        'picture',
        'max_streak',
        'max_streak_name',
        'weekly_challenges_finished',
        'weekly_challenges_failed',
        'promises_finished',
    ];
}
