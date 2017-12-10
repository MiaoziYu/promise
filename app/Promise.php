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

    public function getFormattedDateAttribute()
    {
        return $this->finished_at->format('F j, Y');
    }

    public function getFormattedTimeAttribute()
    {
        return $this->finished_at->format('g:ia');
    }
}
