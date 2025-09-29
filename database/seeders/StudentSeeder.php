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
        // Create a user account for the student
        $studentUser = User::updateOrCreate(
            ['email' => 'seanrodelserrera@student.com'],
            [
                'first_name'      => 'Sean Rodel',
                'middle_name'     => 'Main',
                'last_name'       => 'Serrera',
                'role'            => 'student',
                'contact_number'  => '09123456789',
                'address'         => 'Brgy. San Miguel, Cityville',
                'password'        => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create the student profile
        Student::updateOrCreate(
            ['user_id' => $studentUser->id],
            [
                'student_number'       => 'STU0001',
                'lrn'                  => '123456789012',
                'strand'               => 'STEM',
                'grade_level'          => 'Grade 11',
                'special_needs'        => null,

                // Personal information
                'birthdate'            => '2008-05-10',
                'gender'               => 'Male',
                'civil_status'         => 'Single',
                'nationality'          => 'Filipino',
                'religion'             => 'Roman Catholic',
                'contact_number'       => '09123456789',
                'address'              => 'Brgy. San Miguel, Cityville',

                // Parent/guardian information
                'father_name'          => 'Rodel Serrera',
                'father_contact'       => '09111111111',
                'father_occupation'    => 'Engineer',
                'mother_name'          => 'Maria Serrera',
                'mother_contact'       => '09222222222',
                'mother_occupation'    => 'Teacher',
                'guardian_name'        => 'Rodel Serrera',
                'guardian_contact'     => '09111111111',
                'guardian_relationship'=> 'Father',
            ]
        );
    }
}
