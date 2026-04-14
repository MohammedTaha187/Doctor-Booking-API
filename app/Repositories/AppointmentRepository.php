<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Repositories\Interfaces\AppointmentRepositoryInterface;

class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function create(array $data)
    {
        return Appointment::create($data);
    }

    public function update(string $id, array $data)
    {
        $record = Appointment::find($id);
        if ($record) {
            $record->update($data);

            return $record;
        }

        return null;
    }

    public function delete(string $id)
    {
        $record = Appointment::find($id);
        if ($record) {
            return $record->delete();
        }

        return false;
    }

    public function find(string $id)
    {
        return Appointment::find($id);
    }

    public function all()
    {
        return Appointment::all();
    }

    public function getByDoctorId(string $doctorId)
    {
        return Appointment::where('doctor_id', $doctorId)->get();
    }

    public function getByPatientId(string $patientId)
    {
        return Appointment::where('patient_id', $patientId)->get();
    }
}
