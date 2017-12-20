<?php

use Faker\Generator as Faker;

/* @var Illuminate\Database\Eloquent\Factory $factory */

$factory->define(App\Checklist::class, function (Faker $faker) {
    return [
        'text' => 'go to gym',
        'status' => false
    ];
});
