<?php

namespace App\Jobs;

use App\Mail\AppointmentReminderMail;
use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public Appointment $appointment) {}

    /**
     * Send reminder emails to both patient and doctor 24h before the appointment.
     */
    public function handle(): void
    {
        $appointment = $this->appointment->load(['patient', 'doctor.user']);

        if (in_array($appointment->status, ['cancelled', 'completed', 'no_show'])) {
            return;
        }

        // Notify Patient
        Mail::to($appointment->patient->email)->send(
            new AppointmentReminderMail($appointment, 'patient')
        );

        // Notify Doctor
        Mail::to($appointment->doctor->user->email)->send(
            new AppointmentReminderMail($appointment, 'doctor')
        );
    }
}
