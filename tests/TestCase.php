<?php

namespace Tests;

use App\Models\Specialty;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles for every test
        $this->seed(RolesSeeder::class);
    }

    /**
     * Create a user with a specific role.
     */
    protected function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    /**
     * Helper to act as an admin.
     */
    protected function actingAsAdmin(): User
    {
        $user = $this->createUserWithRole('admin');
        $token = auth('api')->login($user);
        $this->withHeader('Authorization', 'Bearer '.$token);

        return $user;
    }

    /**
     * Helper to act as a doctor.
     */
    protected function actingAsDoctor(): User
    {
        $user = User::factory()->create();
        $user->assignRole('doctor');

        $specialty = Specialty::first() ?? Specialty::factory()->create(['slug' => 'general']);
        $user->doctor()->create([
            'specialty_id' => $specialty->id,
            'license_number' => 'LIC-'.rand(1000, 9999),
            'years_experience' => 10,
            'consultation_type' => 'both',
        ]);

        $token = auth('api')->login($user);
        $this->withHeader('Authorization', 'Bearer '.$token);

        return $user;
    }

    /**
     * Helper to act as a patient.
     */
    protected function actingAsPatient(): User
    {
        $user = User::factory()->create();
        $user->assignRole('patient');

        $token = auth('api')->login($user);
        $this->withHeader('Authorization', 'Bearer '.$token);

        return $user;
    }
}
