<?php

namespace App\Http\Controllers\Api\V1\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Review;
use App\Services\Api\V1\Doctor\DoctorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(protected DoctorService $doctorService) {}

    /**
     * Get doctor-specific statistics.
     */
    public function index(Request $request): JsonResponse
    {
        $doctor = $this->doctorService->getProfileByUserId($request->user()->id);

        if (! $doctor) {
            return response()->json(['message' => 'Doctor profile not found'], 404);
        }

        $stats = [
            'overview' => [
                'total_appointments' => Appointment::where('doctor_id', $doctor->id)->count(),
                'upcoming_appointments' => Appointment::where('doctor_id', $doctor->id)
                    ->where('scheduled_date', '>=', now())
                    ->where('status', 'confirmed')
                    ->count(),
                'avg_rating' => $doctor->rating,
                'total_reviews' => $doctor->reviews_count,
            ],
            'recent_reviews' => Review::where('doctor_id', $doctor->id)
                ->where('is_approved', true)
                ->with('patient')
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get(),
            'today_schedule' => Appointment::where('doctor_id', $doctor->id)
                ->where('scheduled_date', now()->toDateString())
                ->with('patient')
                ->orderBy('scheduled_time', 'asc')
                ->get(),
        ];

        return response()->json($stats);
    }
}
