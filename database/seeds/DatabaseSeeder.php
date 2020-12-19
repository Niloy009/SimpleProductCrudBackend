<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {


        $user = factory(User::class)->create([
            'name' => 'User',
            'email' => 'user@mail.com',
        ]);

        factory(Product::class, 20)->create();
        factory(User::class, 20)->create();

    }
}
