<?php

// database/seeders/AdminSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create an admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@imposter-game.com',
            'role' => 'admin',
            'supabase_id' => 'admin-' . uniqid(),
        ]);

        $this->command->info('Admin user created successfully.');
    }
}