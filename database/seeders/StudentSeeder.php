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
        /*
        |--------------------------------------------------------------------------
        | Student 1: Sean Rodel Serrera
        |--------------------------------------------------------------------------
        */
        $studentUser1 = User::updateOrCreate(
            ['email' => 'jerome.morales@lccdo.edu.ph'],
            [
                'first_name'        => 'Jerome',
                'middle_name'       => 'Main',
                'last_name'         => 'Morales',
                'role'              => 'student',
                'contact_number'    => '09123456789',
                'address'           => 'Brgy. San Miguel, Cityville',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        Student::updateOrCreate(
            ['user_id' => $studentUser1->id],
            [
                'student_number'        => 'STU0001',
                'lrn'                   => '123456789012',
                'strand'                => 'STEM',
                'grade_level'           => '11',
                'special_needs'         => null,
                'birthdate'             => '2008-05-10',
                'gender'                => 'Male',
                'civil_status'          => 'Single',
                'nationality'           => 'Filipino',
                'religion'              => 'Roman Catholic',
                'contact_number'        => '09123456789',
                'address'               => 'Brgy. San Miguel, Cityville',
                'father_name'           => 'Rodel Serrera',
                'father_contact'        => '09111111111',
                'father_occupation'     => 'Engineer',
                'mother_name'           => 'Maria Serrera',
                'mother_contact'        => '09222222222',
                'mother_occupation'     => 'Teacher',
                'guardian_name'         => 'Rodel Serrera',
                'guardian_contact'      => '09111111111',
                'guardian_relationship' => 'Father',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Student 2: Rey Rameses Jude Sanchez Macalutas III
        |--------------------------------------------------------------------------
        */
        $studentUser2 = User::updateOrCreate(
            ['email' => '1901102366@lccdo.edu.ph'],
            [
                'first_name'        => 'Rey Rameses Jude',
                'middle_name'       => 'Sanchez',
                'last_name'         => 'Macalutas III',
                'role'              => 'student',
                'contact_number'    => '09998887777',
                'address'           => 'Brgy. Casisang, Malaybalay City',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        Student::updateOrCreate(
            ['user_id' => $studentUser2->id],
            [
                'student_number'        => 'STU0002',
                'lrn'                   => '987654321098',
                'strand'                => 'ABM',
                'grade_level'           => '12',
                'special_needs'         => null,
                'birthdate'             => '2007-03-15',
                'gender'                => 'Male',
                'civil_status'          => 'Single',
                'nationality'           => 'Filipino',
                'religion'              => 'Roman Catholic',
                'contact_number'        => '09998887777',
                'address'               => 'Brgy. Casisang, Malaybalay City',
                'father_name'           => 'Rey Macalutas Jr.',
                'father_contact'        => '09112223333',
                'father_occupation'     => 'Farmer',
                'mother_name'           => 'Judea Sanchez',
                'mother_contact'        => '09114445555',
                'mother_occupation'     => 'Businesswoman',
                'guardian_name'         => 'Rey Macalutas Jr.',
                'guardian_contact'      => '09112223333',
                'guardian_relationship' => 'Father',
            ]
        );

        Student::factory()->count(1295)->create();
    }
}
