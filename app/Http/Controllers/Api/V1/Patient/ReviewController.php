<?php

namespace App\Http\Controllers\Api\V1\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Review\StoreReviewRequest;
use App\Models\Appointment;
use App\Models\Review;
use App\Services\Api\V1\Review\ReviewService;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function __construct(protected ReviewService $reviewService) {}

    /**
     * Submit a review for a completed appointment.
     */
    public function store(StoreReviewRequest $request): JsonResponse
    {
        $appointment = Appointment::find($request->appointment_id);

        if (! $appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        $this->authorize('create', [Review::class, $appointment]);

        try {
            $data = $request->validated();
            $data['patient_id'] = $request->user()->id;
            $data['doctor_id'] = $appointment->doctor_id;
            $data['is_approved'] = false; // Default to needs moderation

            $review = $this->reviewService->submitReview($data);

            return response()->json([
                'message' => 'Review submitted successfully. It will be visible after approval.',
                'data' => $review,
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
