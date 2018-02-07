<?php

use App\Habit;
use Faker\Generator as Faker;

/* @var Illuminate\Database\Eloquent\Factory $factory */

$factory->define(Habit::class, function (Faker $faker) {
    return [
        'name' => 'learn german',
        'description' => 'description',
        'credits' => 5,
        'count' => 0,
        'streak' => 0,
        'max_streak' => 0,
        'checked_at' => null
    ];
});
