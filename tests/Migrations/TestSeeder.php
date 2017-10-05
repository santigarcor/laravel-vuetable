<?php

namespace Vuetable\Tests\Migrations;

use Vuetable\Tests\Models\Car;
use Illuminate\Database\Seeder;
use Vuetable\Tests\Models\User;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userA = User::create(['name' => 'John Doe', 'email' => 'john@mail.com']);
        $userB = User::create(['name' => 'Jane Doe', 'email' => 'jane@mail.com']);
        $userC = User::create(['name' => 'Test John', 'email' => 'test@mail.com']);

        $userACar1 = Car::create(['name' => 'Car A', 'user_id' => $userA->id]);
        $userACar2 = Car::create(['name' => 'Car B', 'user_id' => $userA->id]);
        $userBCar = Car::create(['name' => 'Car C', 'user_id' => $userB->id]);
        $userCCar = Car::create(['name' => 'Car D', 'user_id' => $userC->id]);
    }
}
