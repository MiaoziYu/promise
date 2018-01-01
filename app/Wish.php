<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wish extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'credits',
        'image_link',
        'purchased_at'
    ];
}
