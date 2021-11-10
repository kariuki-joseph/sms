<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Exams;
use Faker\Generator as Faker;

$factory->define(Exams::class, function (Faker $faker) {
    return [
        'name'=>$faker->name,
        'exam_id'=>rand(1,1000),
    ];
});
