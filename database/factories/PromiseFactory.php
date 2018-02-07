<?php

use Faker\Generator as Faker;

/* @var Illuminate\Database\Eloquent\Factory $factory */

$factory->define(App\Promise::class, function (Faker $faker) {
    return [
        'name' => 'example name',
        'description' => 'example description',
        'punch_card_total' => 10,
        'punch_card_finished' => 5,
        'credits' => '500',
        'due_date' => null,
        'expired' => null,
        'finished_at' => null,
    ];
});
