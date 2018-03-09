<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loot extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'drop_rate',
        'rarity',
        'value',
    ];

    protected $dates = [
        'deleted_at'
    ];

    public function users()
    {
        return $this->belongsToMany('App\User', 'loot_user')->withPivot('applied_at');
    }
}
