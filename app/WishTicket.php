<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WishTicket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'wish_id',
        'claimed_at'
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected $appends = [
        'wish',
        'formatted_claimed_date',
        'owners'
    ];

    public function users()
    {
        return $this->belongsToMany('App\User', 'user_wish_ticket', 'wish_ticket_id', 'user_id');
    }

    public function wish()
    {
        return $this->belongsTo('App\Wish');
    }

    public function scopeUnclaimed($query)
    {
        return $query->where('claimed_at', null);
    }

    public function scopeClaimed($query)
    {
        return $query->where('claimed_at', '!=', null);
    }

    public function getWishAttribute()
    {
        return $this->wish()->get();
    }

    public function getFormattedClaimedDateAttribute()
    {
        if (!$this->claimed_at) {
            return null;
        }

        return Carbon::parse($this->claimed_at)->format('F j, Y');
    }

    public function getOwnersAttribute()
    {
        return $this->users()->with('userProfile')->get();
    }
}
