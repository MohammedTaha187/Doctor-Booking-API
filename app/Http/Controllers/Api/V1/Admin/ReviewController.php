<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Review\ReviewResource;
use App\Models\Review;
use App\Services\Api\V1\Review\ReviewService;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function __construct(protected ReviewService $reviewService) {}

    /**
     * List all reviews (pending first).
     */
    public function index(): JsonResponse
    {
        $reviews = Review::with([
            'patient',
            'doctor.user',
            'doctor.specialty',
            'doctor.translations',
            'doctor.specialty.translations',
            'appointment',
        ])
            ->orderBy('is_approved', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json(ReviewResource::collection($reviews)->response()->getData(true));
    }

    /**
     * Approve a review.
     */
    public function approve(string $id): JsonResponse
    {
        try {
            $review = $this->reviewService->approveReview($id);

            return response()->json([
                'message' => 'Review approved and doctor rating updated.',
                'data' => $review,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    /**
     * Delete a review.
     */
    public function destroy(string $id): JsonResponse
    {
        $review = Review::find($id);
        if (! $review) {
            return response()->json(['message' => 'Review not found'], 404);
        }

        $doctorId = $review->doctor_id;
        $review->delete();

        // Recalculate rating after deletion
        $this->reviewService->updateDoctorRating($doctorId);

        return response()->json(null, 204);
    }
}
