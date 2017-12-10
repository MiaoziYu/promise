<?php

use Faker\Generator as Faker;

/* @var Illuminate\Database\Eloquent\Factory $factory */

$factory->define(App\Promise::class, function (Faker $faker) {
    return [
        'title' => 'KFC hot wings',
        'description' => '18 kfc hot wings',
        'check_list_quantity' => '18',
        'check_list_finished' => '14',
        'finished_at' => null,
    ];
});
