<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promise extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'check_list_quantity',
        'check_list_finished',
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

    public function createChecklist($text)
    {
        return $this->checklists()->create([
            'text' => $text,
            'status' => false
        ]);
    }
}
