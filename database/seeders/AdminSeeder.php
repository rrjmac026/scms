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
        User::updateOrCreate(
            ['email' => 'admin@admin.com'], // unique check
            [
                'first_name' => 'System',
                'middle_name' => 'Main',
                'last_name' => 'Administrator',
                'role' => 'admin',
                'contact_number' => '09123456789',
                'address' => 'University Campus',
                'password' => Hash::make('password'), // secure hash
                'email_verified_at' => now(),
            ]
        );
    }
}
