<?php

use App\Models\Posts;
use App\Models\Users;
use Phalcon\Security;

$factory->define(Users::class, function (Faker\Generator $faker) use ($factory) {
    return [
        'username' => $faker->userName,
        'email'    => $faker->unique()->safeEmail,
        'password' => $factory->security->hash('password'),
    ];
});

$factory->define(Users::class, function (Faker\Generator $faker) {
    return [
        'username' => 'myUsername',
        'email'    => 'myEmail',
        'password' => 'myPassword',
    ];
}, 'myUser');

$factory->define(Posts::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->unique()->sentence(4, true),
        'body'  => $faker->paragraph(4, true),
    ];
});

$factory->define(Posts::class, function (Faker\Generator $faker) {
    return [
        'title'    => $faker->unique()->sentence(4, true),
        'body'     => $faker->paragraph(4, true),
        'users_id' => function () {
            return factory(Users::class)->create()->id;
        },
    ];
}, 'withUser');
