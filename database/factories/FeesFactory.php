<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Payable;
use Faker\Generator as Faker;

$factory->define(Payable::class, function (Faker $faker) {
    return [
        'name'=>$faker->name,
        'amount'=>$faker->randomDigit,
        'upcoming'=>0
    ];
});
