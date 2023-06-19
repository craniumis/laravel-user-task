<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $user = User::where("email",'john_doe@domain.com')->first();
        if (empty($user)){
            User::create([
                "first_name" => "John",
                "last_name" => "Doe",
                "email" => "john_doe@domain.com",
                "password" => Hash::make("12345678"),
                "photo" => 0
            ]);
        }
    }
}
