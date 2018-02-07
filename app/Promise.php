<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promise extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'punch_card_total',
        'punch_card_finished',
        'credits',
        'due_date',
        'expired',
        'finished_at',
        'order'
    ];

    protected $dates = [
        'deleted_at'
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
