<?php

namespace Tests\Feature\Patient;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\TimeSlot;
use App\Models\Translation;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    /**
     * Test patient can list appointments with nested relations loaded.
     */
    public function test_patient_can_list_appointments_with_nested_relations(): void
    {
        $patient = $this->actingAsPatient();

        $specialty = Specialty::factory()->create();
        Translation::create([
            'translatable_type' => Specialty::class,
            'translatable_id' => $specialty->id,
            'locale' => app()->getLocale(),
            'field' => 'name',
            'value' => 'General Medicine',
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
            'value' => 'General practitioner',
        ]);

        $slot = TimeSlot::factory()->create([
            'doctor_id' => $doctor->id,
        ]);

        Appointment::factory()->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'time_slot_id' => $slot->id,
        ]);

        $response = $this->getJson('/api/v1/patient/appointments');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'doctor' => [
                            'id',
                            'specialty' => [
                                'id',
                                'name',
                            ],
                            'bio',
                        ],
                        'time_slot' => [
                            'id',
                            'start_time',
                            'end_time',
                        ],
                    ],
                ],
            ])
            ->assertJsonPath('data.0.doctor.specialty.name', 'General Medicine')
            ->assertJsonPath('data.0.doctor.bio', 'General practitioner');
    }
}
