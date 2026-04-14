<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'specialty_id' => Specialty::factory(),
            'license_number' => 'LIC-'.fake()->unique()->numberBetween(1000, 9999),
            'years_experience' => fake()->numberBetween(1, 40),
            'consultation_type' => fake()->randomElement(['online', 'in_person', 'both']),
            'is_available' => true,
        ];
    }
}
