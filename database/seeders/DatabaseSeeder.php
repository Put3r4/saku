<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat akun Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@saku.test',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Membuat akun Mahasiswa
        User::create([
            'name' => 'Mahasiswa',
            'email' => 'mahasiswa@saku.test',
            'password' => Hash::make('mahasiswa123'),
            'role' => 'mahasiswa',
        ]);

        // Seed criteria for SAW method
        $this->call(CriterionSeeder::class);

        // Seed menu items with evaluations
        $this->call(MenuSeeder::class);
    }
}