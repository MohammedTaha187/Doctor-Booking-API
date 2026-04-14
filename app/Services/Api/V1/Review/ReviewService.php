<?php

namespace App\Services\Api\V1\Review;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    /**
     * Submit a review for a completed appointment.
     */
    public function submitReview(array $data)
    {
        $appointment = Appointment::findOrFail($data['appointment_id']);

        if ($appointment->status !== 'completed') {
            throw new \Exception('You can only review completed appointments.');
        }

        return Review::create($data);
    }

    /**
     * Approve a review and update doctor's rating.
     */
    public function approveReview(string $id)
    {
        return DB::transaction(function () use ($id) {
            $review = Review::findOrFail($id);
            $review->update(['is_approved' => true]);

            $this->updateDoctorRating($review->doctor_id);

            return $review;
        });
    }

    /**
     * Update doctor average rating and count.
     */
    public function updateDoctorRating(string $doctorId)
    {
        $stats = Review::where('doctor_id', $doctorId)
            ->where('is_approved', true)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as count')
            ->first();

        Doctor::where('id', $doctorId)->update([
            'rating' => round($stats->avg_rating, 1),
            'reviews_count' => $stats->count,
        ]);
    }
}
