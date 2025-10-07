<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        // Create a random student number for email prefix
        $studentNumber = $this->faker->unique()->numerify('19#########');

        return [
            'first_name'        => $this->faker->firstName(),
            'middle_name'       => $this->faker->lastName(),
            'last_name'         => $this->faker->lastName(),
            'email'             => "{$studentNumber}@lccdo.edu.ph",
            'role'              => 'student',
            'contact_number'    => $this->faker->phoneNumber(),
            'address'           => $this->faker->address(),
            'password'          => static::$password ??= Hash::make('password'),
            'email_verified_at' => now(),
            'remember_token'    => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
