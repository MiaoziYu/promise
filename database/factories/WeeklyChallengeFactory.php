<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\WeeklyChallenge::class, function (Faker $faker) {
    return [
        'name' => 'example name',
        'description' => 'example description',
        'credits' => 10,
        'goal' => 2,
        'count' => 0,
        'failed' => 0
    ];
});
