<?php

namespace App\Http\Controllers\Api\V1\Patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Doctor\DoctorResource;
use App\Http\Resources\Api\V1\TimeSlot\TimeSlotResource;
use App\Repositories\Interfaces\DoctorRepositoryInterface;
use App\Services\Api\V1\Appointment\SlotAvailabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function __construct(
        protected DoctorRepositoryInterface $doctorRepository,
        protected SlotAvailabilityService $slotAvailabilityService
    ) {}

    /**
     * Search and filter doctors.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['specialty_id', 'name', 'min_fee', 'max_fee', 'per_page']);
        $doctors = $this->doctorRepository->search($filters);

        return response()->json(DoctorResource::collection($doctors)->response()->getData(true));
    }

    /**
     * View specific doctor details.
     */
    public function show(string $id): JsonResponse
    {
        $doctor = $this->doctorRepository->find($id);
        if (! $doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        return response()->json(new DoctorResource($doctor));
    }

    /**
     * Get real-time available slots for a doctor on a specific date.
     */
    public function availableSlots(string $id, Request $request): JsonResponse
    {
        $request->validate(['date' => 'required|date|after_or_equal:today']);

        $doctor = $this->doctorRepository->find($id);
        if (! $doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        $slots = $this->slotAvailabilityService->getAvailableSlots($id, $request->date);

        return response()->json(TimeSlotResource::collection($slots));
    }
}
