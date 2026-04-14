<?php

namespace App\Listeners;

use App\Events\AppointmentBooked;
use App\Events\AppointmentCancelled;
use App\Mail\AppointmentBookedMail;
use App\Mail\AppointmentCancelledMail;
use Illuminate\Support\Facades\Mail;

class NotifyAppointmentParticipants
{
    /**
     * Handle Appointment Booked event.
     */
    public function handleAppointmentBooked(AppointmentBooked $event): void
    {
        $appointment = $event->appointment;
        $appointment->load(['doctor.user', 'patient']);

        // Notify Patient
        Mail::to($appointment->patient->email)
            ->send(new AppointmentBookedMail($appointment, 'patient'));

        // Notify Doctor
        Mail::to($appointment->doctor->user->email)
            ->send(new AppointmentBookedMail($appointment, 'doctor'));
    }

    /**
     * Handle Appointment Cancelled event.
     */
    public function handleAppointmentCancelled(AppointmentCancelled $event): void
    {
        $appointment = $event->appointment;
        $appointment->load(['doctor.user', 'patient']);

        // Notify Patient
        Mail::to($appointment->patient->email)
            ->send(new AppointmentCancelledMail($appointment));

        // Notify Doctor
        Mail::to($appointment->doctor->user->email)
            ->send(new AppointmentCancelledMail($appointment));
    }
}
