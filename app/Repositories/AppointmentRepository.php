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
        $record = Appointment::with([
            'patient',
            'doctor.user',
            'doctor.specialty',
            'doctor.translations',
            'doctor.specialty.translations',
            'timeSlot',
            'payment',
        ])->find($id);
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
        return Appointment::with([
            'patient',
            'doctor.user',
            'doctor.specialty',
            'doctor.translations',
            'doctor.specialty.translations',
            'timeSlot',
            'payment',
        ])->find($id);
    }

    public function all()
    {
        return Appointment::with([
            'patient',
            'doctor.user',
            'doctor.specialty',
            'doctor.translations',
            'doctor.specialty.translations',
            'timeSlot',
            'payment',
        ])->get();
    }

    public function getByDoctorId(string $doctorId)
    {
        return Appointment::with([
            'patient',
            'doctor.user',
            'doctor.specialty',
            'doctor.translations',
            'doctor.specialty.translations',
            'timeSlot',
            'payment',
        ])
            ->where('doctor_id', $doctorId)
            ->get();
    }

    public function getByPatientId(string $patientId)
    {
        return Appointment::with([
            'patient',
            'doctor.user',
            'doctor.specialty',
            'doctor.translations',
            'doctor.specialty.translations',
            'timeSlot',
            'payment',
        ])
            ->where('patient_id', $patientId)
            ->get();
    }
}
