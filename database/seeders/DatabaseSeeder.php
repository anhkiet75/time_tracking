<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        \App\Models\SuperAdmin::factory()->create([
            'name' => 'Super admin',
            'email' => 'sadmin@gmail.com',
            'password' => bcrypt('123'),
        ]);

        $bussines = new \App\Models\Business;
        $bussines->name = 'Facebook';
        $bussines->address = 'USA';
        $bussines->phone_number = '123213';
        $bussines->admin_id = 1;
        $bussines->save();

        \App\Models\User::factory()->create([
            'name' => 'Facebook',
            'email' => 'fb@gmail.com',
            'is_admin' => 1,
            'pin_code' => '123456',
            'business_id' => 1,
            'password' => bcrypt('123'),
        ]);

        \App\Models\User::factory()->create([
            'name' => 'test',
            'email' => 'test@gmail.com',
            'is_admin' => 0,
            'pin_code' => '123456',
            'business_id' => 1,
            'password' => bcrypt('123'),
        ]);
    }
}
