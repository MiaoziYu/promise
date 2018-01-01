<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wish extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'image_link'
    ];
}
