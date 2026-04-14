<?php

namespace App\Policies\Api\V1;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Carbon;

class AppointmentPolicy
{
    /**
     * Patient can see their own appointments; Doctor sees theirs; Admin sees all.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->patient_id
            || $user->doctor?->id === $appointment->doctor_id
            || $user->hasRole('admin');
    }

    /**
     * Only patient can cancel, only if appointment is pending/confirmed and >24h away.
     */
    public function cancel(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->patient_id
            && in_array($appointment->status, ['pending', 'confirmed'])
            && now()->lt(
                Carbon::parse("{$appointment->scheduled_date} {$appointment->scheduled_time}")->subHours(24)
            );
    }

    /**
     * Only the patient who owns the appointment can pay for it.
     */
    public function pay(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->patient_id
            && $appointment->payment_status === 'unpaid'
            && $appointment->status !== 'cancelled';
    }

    /**
     * Only the doctor assigned to this appointment can confirm it.
     */
    public function confirm(User $user, Appointment $appointment): bool
    {
        return $user->doctor?->id === $appointment->doctor_id
            && $appointment->status === 'pending';
    }

    /**
     * Only the assigned doctor can mark appointment as complete.
     */
    public function complete(User $user, Appointment $appointment): bool
    {
        return $user->doctor?->id === $appointment->doctor_id
            && $appointment->status === 'confirmed';
    }
}
