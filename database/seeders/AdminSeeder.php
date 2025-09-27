<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@admin.com'], 
            [
                'first_name' => 'Erven',
                'middle_name' => 'Capili',
                'last_name' => 'Granada',
                'role' => 'admin',
                'contact_number' => '09123456789',
                'address' => 'University Campus',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}
