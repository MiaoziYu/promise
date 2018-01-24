<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wish extends Model
{
    protected $fillable = [
        'owner',
        'name',
        'description',
        'credits',
        'image_link',
        'resolved_at'
    ];

    protected $appends = [
        'owners'
    ];

    public function users()
    {
        return $this->belongsToMany('App\User', 'user_wish')->withPivot('credits');
    }

    public function scopeUnresolved($query)
    {
        return $query->where('resolved_at', null);
    }

    public function scopeResolved($query)
    {
        return $query->where('resolved_at', '!=', null);
    }

    public function getOwnersAttribute()
    {
        return $this->users()->with('userProfile')->get();
    }
}
