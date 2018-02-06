<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Checklist extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'text',
        'status'
    ];

    protected $dates = [
        'deleted_at'
    ];
}
