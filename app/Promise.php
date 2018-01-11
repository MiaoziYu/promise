<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promise extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'punch_card_total',
        'punch_card_finished',
        'reward_type',
        'reward_name',
        'reward_credits',
        'reward_image_link',
        'due_date',
        'expired',
        'finished_at',
        'order'
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

    public function scopeUnexpired($query)
    {
        return $query->where('expired', null)->orWhere('expired', 'pending');
    }
}
