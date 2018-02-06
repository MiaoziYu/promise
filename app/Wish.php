<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wish extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'owner',
        'name',
        'description',
        'credits',
        'image_link',
        'order'
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected $appends = [
        'owners',
    ];

    public function users()
    {
        return $this->belongsToMany('App\User', 'user_wish')->withPivot('credits');
    }

    public function getOwnersAttribute()
    {
        $users = $this->users()->with('userProfile')->get();

        collect($users)->map(function($user) {
            return $user->credits_contributed = $user->pivot->credits;
        });

        return $users;
    }
}
