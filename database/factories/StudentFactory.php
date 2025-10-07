<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'student_number' => $this->faker->unique()->numerify('STU#####'),
            'lrn' => $this->faker->unique()->numerify('LRN##########'),
            'strand' => $this->faker->randomElement(['STEM', 'ABM', 'HUMSS', 'GAS', 'TVL']),
            'grade_level' => $this->faker->randomElement(['11', '12']),
            'special_needs' => $this->faker->boolean ? $this->faker->word() : null,


            'birthdate' => $this->faker->date('Y-m-d', '2010-01-01'),
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'address' => $this->faker->address(),
            'contact_number' => $this->faker->phoneNumber(),
            'civil_status' => $this->faker->randomElement(['Single', 'Married']),
            'nationality' => $this->faker->country(),
            'religion' => $this->faker->word(),


            'father_name' => $this->faker->name('male'),
            'father_contact' => $this->faker->phoneNumber(),
            'father_occupation' => $this->faker->jobTitle(),
            'mother_name' => $this->faker->name('female'),
            'mother_contact' => $this->faker->phoneNumber(),
            'mother_occupation' => $this->faker->jobTitle(),
            'guardian_name' => $this->faker->name(),
            'guardian_contact' => $this->faker->phoneNumber(),
            'guardian_relationship' => $this->faker->randomElement(['Father', 'Mother', 'Sibling', 'Uncle', 'Aunt', 'Other']),
        ];
    }
}
