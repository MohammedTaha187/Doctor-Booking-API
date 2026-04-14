<?php

namespace App\Services\Api\V1\Doctor;

use App\Models\Doctor;
use App\Repositories\Interfaces\DoctorRepositoryInterface;

class DoctorService
{
    public function __construct(protected DoctorRepositoryInterface $doctorRepository) {}

    public function getProfileByUserId(string $userId)
    {
        return $this->doctorRepository->findByUserId($userId);
    }

    public function updateProfile(Doctor $doctor, array $data)
    {
        return $this->doctorRepository->update($doctor->id, $data);
    }
}
