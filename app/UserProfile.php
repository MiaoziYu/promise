<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'picture',
        'credits',
        'credits_earned',
        'credits_contributed',
        'max_streak',
        'max_streak_name',
        'promises_finished',
        'weekly_challenges_finished',
        'weekly_challenges_failed',
        'wish_tickets_amount',
    ];
}
