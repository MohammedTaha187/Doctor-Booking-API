<?php

namespace Tests\Feature\Patient;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\TimeSlot;
use Tests\TestCase;

class BookingTest extends TestCase
{
    /**
     * Test patient can book an available slot.
     */
    public function test_patient_can_book_available_slot(): void
    {
        $patient = $this->actingAsPatient();

        $specialty = Specialty::factory()->create();
        $doctorUser = $this->createUserWithRole('doctor');
        $doctor = Doctor::factory()->create([
            'user_id' => $doctorUser->id,
            'specialty_id' => $specialty->id,
        ]);

        $slot = TimeSlot::factory()->create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 'monday',
            'is_available' => true,
        ]);

        $response = $this->postJson('/api/v1/patient/appointments', [
            'doctor_id' => $doctor->id,
            'time_slot_id' => $slot->id,
            'scheduled_date' => now()->next('Monday')->toDateString(),
            'type' => 'in_person',
            'notes' => 'Regular checkup',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->id,
            'time_slot_id' => $slot->id,
        ]);
    }

    /**
     * Test patient cannot book already taken slot.
     */
    public function test_patient_cannot_book_taken_slot(): void
    {
        $this->actingAsPatient();

        $specialty = Specialty::factory()->create();
        $doctorUser = $this->createUserWithRole('doctor');
        $doctor = Doctor::factory()->create([
            'user_id' => $doctorUser->id,
            'specialty_id' => $specialty->id,
        ]);

        $slot = TimeSlot::factory()->create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 'monday',
            'is_available' => true,
        ]);

        $date = now()->next('Monday')->toDateString();

        // Book it once
        $this->postJson('/api/v1/patient/appointments', [
            'doctor_id' => $doctor->id,
            'time_slot_id' => $slot->id,
            'scheduled_date' => $date,
            'type' => 'in_person',
        ]);

        // Attempt to book same slot again
        $response = $this->postJson('/api/v1/patient/appointments', [
            'doctor_id' => $doctor->id,
            'time_slot_id' => $slot->id,
            'scheduled_date' => $date,
            'type' => 'in_person',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test patient cannot book a slot that belongs to another doctor.
     */
    public function test_patient_cannot_book_slot_from_different_doctor(): void
    {
        $patient = $this->actingAsPatient();

        $specialty = Specialty::factory()->create();
        $firstDoctorUser = $this->createUserWithRole('doctor');
        $secondDoctorUser = $this->createUserWithRole('doctor');

        $firstDoctor = Doctor::factory()->create([
            'user_id' => $firstDoctorUser->id,
            'specialty_id' => $specialty->id,
        ]);

        $secondDoctor = Doctor::factory()->create([
            'user_id' => $secondDoctorUser->id,
            'specialty_id' => $specialty->id,
        ]);

        $slot = TimeSlot::factory()->create([
            'doctor_id' => $secondDoctor->id,
            'day_of_week' => 'monday',
            'is_available' => true,
        ]);

        $response = $this->postJson('/api/v1/patient/appointments', [
            'doctor_id' => $firstDoctor->id,
            'time_slot_id' => $slot->id,
            'scheduled_date' => now()->next('Monday')->toDateString(),
            'type' => 'in_person',
            'notes' => 'Mismatch booking',
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseMissing('appointments', [
            'patient_id' => $patient->id,
            'time_slot_id' => $slot->id,
        ]);
    }
}
