<?php

use Faker\Generator as Faker;

$factory->define(App\Models\UserProfile::class, function (Faker $faker) {
    return [
        'user_id' => factory(\App\User::class),
        'bio' =>$faker->paragraph,
    ];
});
