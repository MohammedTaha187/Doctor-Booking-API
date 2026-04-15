<?php

namespace App\Services\Api\V1\Appointment;

use App\Events\AppointmentBooked;
use App\Events\AppointmentCancelled;
use App\Events\AppointmentCompleted;
use App\Models\Appointment;
use App\Repositories\Interfaces\AppointmentRepositoryInterface;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    public function __construct(
        protected AppointmentRepositoryInterface $appointmentRepository,
        protected SlotAvailabilityService $slotAvailabilityService
    ) {}

    /**
     * Book a new appointment with strict availability check.
     */
    public function bookAppointment(array $data)
    {
        return DB::transaction(function () use ($data) {
            $isAvailable = $this->slotAvailabilityService->isSlotAvailable(
                $data['time_slot_id'],
                $data['scheduled_date']
            );

            if (! $isAvailable) {
                throw new \Exception('This slot is no longer available for the selected date.');
            }

            $appointment = $this->appointmentRepository->create($data);

            event(new AppointmentBooked($appointment));

            return $appointment;
        });
    }

    public function updateStatus(string $id, string $status)
    {
        $appointment = $this->appointmentRepository->update($id, ['status' => $status]);

        if ($appointment) {
            match ($status) {
                'cancelled' => event(new AppointmentCancelled($appointment)),
                'completed' => event(new AppointmentCompleted($appointment)),
                default => null,
            };
        }

        return $appointment;
    }

    public function getForDoctor(string $doctorId)
    {
        return $this->appointmentRepository->getByDoctorId($doctorId);
    }

    public function getForPatient(string $patientId)
    {
        return $this->appointmentRepository->getByPatientId($patientId);
    }

    public function find(string $id)
    {
        return $this->appointmentRepository->find($id);
    }
}
