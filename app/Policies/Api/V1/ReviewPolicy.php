<?php

namespace App\Policies\Api\V1;

use App\Models\Appointment;
use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Only the patient who completed the appointment can submit a review — and only once.
     */
    public function create(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->patient_id
            && $appointment->status === 'completed'
            && ! Review::where('appointment_id', $appointment->id)->exists();
    }
}
