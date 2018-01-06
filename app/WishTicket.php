<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WishTicket extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'image_link',
        'claimed_at'
    ];

    public function scopeUnclaimed($query)
    {
        return $query->where('claimed_at', null);
    }
}
