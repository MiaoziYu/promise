<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loot extends Model
{

    protected $fillable = [
        'type',
        'name',
        'drop_rate',
        'rarity',
        'value',
    ];

    public function users()
    {
        return $this->belongsToMany('App\User', 'loot_user')->withPivot('applied_at');
    }
}
