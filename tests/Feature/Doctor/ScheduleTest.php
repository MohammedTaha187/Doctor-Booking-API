<?php

namespace Tests\Feature\Doctor;

use App\Models\TimeSlot;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    /**
     * Test doctor can create time slots.
     */
    public function test_doctor_can_create_schedule(): void
    {
        $this->actingAsDoctor();

        $response = $this->postJson('/api/v1/doctor/schedule', [
            'day_of_week' => 'monday',
            'start_time' => '09:00',
            'end_time' => '09:30',
            'duration_minutes' => 30,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('time_slots', [
            'day_of_week' => 'monday',
            'start_time' => '09:00:00',
        ]);
    }

    /**
     * Test doctor update/create overlapping slots on same day.
     * New logic: It is idempotent and should return 201/200 instead of 422.
     */
    public function test_doctor_can_handle_overlapping_slots_idempotently(): void
    {
        $user = $this->actingAsDoctor();
        $doctor = $user->doctor;

        TimeSlot::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 'monday',
            'start_time' => '09:00:00',
            'end_time' => '09:30:00',
            'duration_minutes' => 30,
            'is_available' => true,
        ]);

        $response = $this->postJson('/api/v1/doctor/schedule', [
            'day_of_week' => 'monday',
            'start_time' => '09:15',
            'end_time' => '09:45',
            'duration_minutes' => 30,
        ]);

        // In new logic, it should succeed (updated or created)
        $response->assertStatus(201);
    }

    /**
     * Test doctor endpoints return not found when profile is missing.
     */
    public function test_doctor_without_profile_gets_not_found_on_schedule(): void
    {
        $user = $this->createUserWithRole('doctor');
        $token = auth('api')->login($user);
        $this->withHeader('Authorization', 'Bearer '.$token);

        $response = $this->getJson('/api/v1/doctor/schedule');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Doctor profile not found',
            ]);
    }
}
