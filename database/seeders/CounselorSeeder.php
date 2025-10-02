<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Counselor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CounselorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a specific counselor
        $counselorUser = User::updateOrCreate(
            ['email' => 'angelagrachetteestenzo@university.com'],
            [
                'first_name' => 'Angela',
                'middle_name' => 'Cena',
                'last_name' => 'Estenzo',
                'role' => 'counselor',
                'contact_number' => '09123456789',
                'address' => 'University Campus',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        Counselor::updateOrCreate(
            ['user_id' => $counselorUser->id],
            [
                'employee_number' => 'EMP001',
                'counseling_category_id' => null,
                'availability_schedule' => null,
            ]
        );
    }
}
