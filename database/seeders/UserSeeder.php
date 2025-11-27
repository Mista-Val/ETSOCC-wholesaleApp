<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        if(empty(User::count())){
            User::create([
                'name' => 'Admin',
                'role_id' => 1,
                'email' => 'admin@mailinator.com',
                'password' => Hash::make('Admin@123'),
            ]);
        }
    }
}
