<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function userProfile()
    {
        return $this->hasOne('App\UserProfile');
    }

    public function promises()
    {
        return $this->hasMany('App\Promise');
    }

    public function checklists()
    {
        return $this->hasMany('App\Checklist');
    }

    public function updateCredits($promiseId)
    {
        $promise = $this->promises()->findOrFail($promiseId);
        if ($promise->reward_type === 'points') {
            $this->userProfile()->update([
                'credits' => $promise->reward_content
            ]);
        }
    }
}
