<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Counselor;
use Illuminate\Support\Facades\Hash;

class CounselorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Counselor 1: Angela Grachette Cena Estenzo
        |--------------------------------------------------------------------------
        */
        $counselorUser1 = User::updateOrCreate(
            ['email' => 'angela.estenzo@lccdo.edu.ph'],
            [
                'first_name'       => 'Angela',
                'middle_name'      => 'Cena',
                'last_name'        => 'Estenzo',
                'role'             => 'counselor',
                'contact_number'   => '09123456789',
                'address'          => 'University Campus',
                'password'         => Hash::make('password'),
                'email_verified_at'=> now(),
            ]
        );

        Counselor::updateOrCreate(
            ['user_id' => $counselorUser1->id],
            [
                'employee_number'       => 'EMP001',
                'availability_schedule' => [
                    'monday'    => '8:00 AM - 12:00 PM',
                    'wednesday' => '1:00 PM - 5:00 PM',
                ],
                'assigned_grade_level'  => '11',
                'gender'                => 'Female',
                'birth_date'            => '1990-06-12',
                'bio'                   => 'Passionate about guiding students through academic and personal challenges.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Counselor 2: Mark Anthony Dela Cruz
        |--------------------------------------------------------------------------
        */
        $counselorUser2 = User::updateOrCreate(
            ['email' => 'markanthony.delacruz@lccdo.edu.ph'],
            [
                'first_name'       => 'Mark Anthony',
                'middle_name'      => 'Reyes',
                'last_name'        => 'Dela Cruz',
                'role'             => 'counselor',
                'contact_number'   => '09998887777',
                'address'          => 'University Campus',
                'password'         => Hash::make('password'),
                'email_verified_at'=> now(),
            ]
        );

        Counselor::updateOrCreate(
            ['user_id' => $counselorUser2->id],
            [
                'employee_number'       => 'EMP002',
                'availability_schedule' => [
                    'tuesday'   => '9:00 AM - 3:00 PM',
                    'thursday'  => '10:00 AM - 4:00 PM',
                ],
                'assigned_grade_level'  => '12',
                'gender'                => 'Male',
                'birth_date'            => '1988-03-25',
                'bio'                   => 'Dedicated to supporting students with career counseling and life coaching.',
            ]
        );
    }
}
