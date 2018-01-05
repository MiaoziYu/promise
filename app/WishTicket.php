<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WishTicket extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'image_link',
        'used_at'
    ];

    public function scopeUnused($query)
    {
        return $query->where('used_at', null);
    }
}
