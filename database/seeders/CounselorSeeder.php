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
            ['email' => 'jane.counselor@university.com'],
            [
                'first_name' => 'Jane',
                'middle_name' => 'A.',
                'last_name' => 'Doe',
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
                'specialization' => 'Academic & Career Counseling',
                'availability_schedule' => [
                    'days' => ['monday', 'tuesday'], // Make sure days are lowercase
                    'times' => ['09:00', '10:00', '11:00', '13:00', '14:00', '15:00']
                ]
            ]
        );
    }
}
