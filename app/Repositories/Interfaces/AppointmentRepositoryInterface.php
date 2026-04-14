<?php

namespace App\Repositories\Interfaces;

interface AppointmentRepositoryInterface
{
    public function create(array $data);

    public function update(string $id, array $data);

    public function delete(string $id);

    public function find(string $id);

    public function all();

    public function getByDoctorId(string $doctorId);

    public function getByPatientId(string $patientId);
}
