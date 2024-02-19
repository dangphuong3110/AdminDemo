<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'username' => 'admin',
            'name' => 'Nguyen Dang Phuong',
            'password' => Hash::make('admin123'),
            'email' => 'nguyendangphuong31102002@gmail.com',
            'role' => 'admin',
            'phone_number' => '0329603xxx',
        ]);
    }
}
