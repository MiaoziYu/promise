<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $fillable = [
        'user_id',
        'subject_id',
        'subject_type',
        'name',
        'value',
    ];
}
