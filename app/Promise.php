<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promise extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'reward_type',
        'reward_content',
        'finished_at'
    ];

    public function checklists()
    {
        return $this->hasMany('App\Checklist');
    }

    public function scopeUnfinished($query)
    {
        return $query->where('finished_at', null);
    }

    public function scopeFinished($query)
    {
        return $query->where('finished_at', '!=', null);
    }
}
