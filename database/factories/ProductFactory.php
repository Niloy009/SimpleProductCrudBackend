<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Product;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Product::class, function (Faker $faker) {

//title must not be unique here i put it unique to solve for fake data in slug
    $title = $faker->unique()->word;
    return [
        'title' => $title,
        'slug' => Str::slug($title),
        'description' => $faker->paragraph . $faker->paragraph,
        'image_url' => 'https://picsum.photos/200/300',
        'price' => mt_rand(10, 200),
        'created_by' => User::all()->random()->id,
    ];
});
