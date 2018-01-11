<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class WishTicket extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'image_link',
        'claimed_at'
    ];

    protected $appends = [
        'formatted_claimed_date'
    ];

    public function scopeUnclaimed($query)
    {
        return $query->where('claimed_at', null);
    }

    public function scopeClaimed($query)
    {
        return $query->where('claimed_at', '!=', null);
    }

    public function getFormattedClaimedDateAttribute()
    {
        if (!$this->claimed_at) {
            return null;
        }

        return Carbon::parse($this->claimed_at)->format('F j, Y');
    }
}
