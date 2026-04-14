<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Review;
use App\Models\Specialty;
use App\Models\TimeSlot;
use App\Models\Translation;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    /**
     * Test patient can submit a review for a completed appointment.
     */
    public function test_patient_can_submit_review_for_completed_appointment(): void
    {
        $patient = $this->actingAsPatient();

        $specialty = Specialty::factory()->create();
        $doctorUser = $this->createUserWithRole('doctor');
        $doctor = Doctor::factory()->create(['user_id' => $doctorUser->id, 'specialty_id' => $specialty->id]);
        $slot = TimeSlot::factory()->create(['doctor_id' => $doctor->id]);

        $appointment = Appointment::factory()->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'time_slot_id' => $slot->id,
            'status' => 'completed',
        ]);

        $response = $this->postJson('/api/v1/patient/reviews', [
            'appointment_id' => $appointment->id,
            'rating' => 5,
            'comment' => 'Great experience!',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reviews', [
            'appointment_id' => $appointment->id,
            'rating' => 5,
        ]);
    }

    /**
     * Test patient cannot review an uncompleted appointment.
     */
    public function test_patient_cannot_review_pending_appointment(): void
    {
        $patient = $this->actingAsPatient();

        $specialty = Specialty::factory()->create();
        $doctorUser = $this->createUserWithRole('doctor');
        $doctor = Doctor::factory()->create(['user_id' => $doctorUser->id, 'specialty_id' => $specialty->id]);
        $slot = TimeSlot::factory()->create(['doctor_id' => $doctor->id]);

        $appointment = Appointment::factory()->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'time_slot_id' => $slot->id,
            'status' => 'pending',
        ]);

        $response = $this->postJson('/api/v1/patient/reviews', [
            'appointment_id' => $appointment->id,
            'rating' => 5,
        ]);

        $response->assertStatus(403); // Forbidden by ReviewPolicy
    }

    /**
     * Test admin can list reviews with nested doctor relations.
     */
    public function test_admin_can_list_reviews_with_nested_relations(): void
    {
        $admin = $this->actingAsAdmin();

        $specialty = Specialty::factory()->create();
        Translation::create([
            'translatable_type' => Specialty::class,
            'translatable_id' => $specialty->id,
            'locale' => app()->getLocale(),
            'field' => 'name',
            'value' => 'Dermatology',
        ]);

        $doctorUser = $this->createUserWithRole('doctor');
        $doctor = Doctor::factory()->create([
            'user_id' => $doctorUser->id,
            'specialty_id' => $specialty->id,
        ]);

        Translation::create([
            'translatable_type' => Doctor::class,
            'translatable_id' => $doctor->id,
            'locale' => app()->getLocale(),
            'field' => 'bio',
            'value' => 'Skin specialist',
        ]);

        $slot = TimeSlot::factory()->create(['doctor_id' => $doctor->id]);

        $appointment = Appointment::factory()->create([
            'patient_id' => $admin->id,
            'doctor_id' => $doctor->id,
            'time_slot_id' => $slot->id,
            'status' => 'completed',
        ]);

        Review::create([
            'patient_id' => $admin->id,
            'doctor_id' => $doctor->id,
            'appointment_id' => $appointment->id,
            'rating' => 4,
            'comment' => 'Helpful visit',
            'is_approved' => false,
        ]);

        $response = $this->getJson('/api/v1/admin/reviews');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'patient',
                        'doctor' => [
                            'id',
                            'specialty' => [
                                'id',
                                'name',
                            ],
                            'bio',
                        ],
                        'appointment',
                    ],
                ],
            ])
            ->assertJsonPath('data.0.doctor.specialty.name', 'Dermatology')
            ->assertJsonPath('data.0.doctor.bio', 'Skin specialist');
    }
}
