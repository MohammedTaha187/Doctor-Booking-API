<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => User::factory(),
            'doctor_id' => Doctor::factory(),
            'time_slot_id' => TimeSlot::factory(),
            'scheduled_date' => fake()->date(),
            'scheduled_time' => fake()->time(),
            'type' => fake()->randomElement(['online', 'in_person']),
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ];
    }
}
