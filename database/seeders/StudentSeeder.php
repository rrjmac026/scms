<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a user for the student
        $studentUser = User::updateOrCreate(
            ['email' => 'seanrodelserrera@student.com'],
            [
                'first_name' => 'Sean Rodel',
                'middle_name' => 'Main',
                'last_name' => 'Serrera',
                'role' => 'student',
                'contact_number' => '09123456789',
                'address' => 'University Campus',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create the student record
        Student::updateOrCreate(
            ['user_id' => $studentUser->id],
            [
                'student_number' => 'STU0001',
                'course' => 'BSCS',
                'year_level' => 1,
                'special_needs' => null,
            ]
        );
    }
}
