<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Students;
use Faker\Generator as Faker;

$factory->define(Students::class, function (Faker $faker) {
    return [
        'name'=>$this->faker->name,
        'class_id'=>$this->faker->randomDigit,
        'adm_number'=>$this->faker->randomDigit
    ];
});
