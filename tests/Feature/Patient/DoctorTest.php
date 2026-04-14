<?php

namespace Tests\Feature\Patient;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\Translation;
use Tests\TestCase;

class DoctorTest extends TestCase
{
    /**
     * Test patient can list doctors with nested profile data.
     */
    public function test_patient_can_list_doctors_with_relations(): void
    {
        $patient = $this->actingAsPatient();

        $specialty = Specialty::factory()->create();
        $doctor = Doctor::factory()->create([
            'specialty_id' => $specialty->id,
        ]);

        Translation::create([
            'translatable_type' => Doctor::class,
            'translatable_id' => $doctor->id,
            'locale' => app()->getLocale(),
            'field' => 'bio',
            'value' => 'General practitioner',
        ]);

        $response = $this->getJson('/api/v1/patient/doctors');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'user' => [
                            'id',
                            'name',
                            'email',
                        ],
                        'specialty' => [
                            'id',
                            'slug',
                        ],
                        'bio',
                    ],
                ],
            ]);
    }

    /**
     * Test patient can view a doctor profile with nested relations.
     */
    public function test_patient_can_view_doctor_details(): void
    {
        $this->actingAsPatient();

        $specialty = Specialty::factory()->create();
        $doctor = Doctor::factory()->create([
            'specialty_id' => $specialty->id,
        ]);

        Translation::create([
            'translatable_type' => Doctor::class,
            'translatable_id' => $doctor->id,
            'locale' => app()->getLocale(),
            'field' => 'bio',
            'value' => 'General practitioner',
        ]);

        $response = $this->getJson("/api/v1/patient/doctors/{$doctor->id}");

        $response->assertOk()
            ->assertJsonPath('id', $doctor->id)
            ->assertJsonStructure([
                'id',
                'user',
                'specialty',
                'bio',
            ]);
    }
}
