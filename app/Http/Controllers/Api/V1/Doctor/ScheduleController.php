<?php

namespace App\Http\Controllers\Api\V1\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\TimeSlot\StoreScheduleRequest;
use App\Models\TimeSlot;
use App\Repositories\Interfaces\TimeSlotRepositoryInterface;
use App\Services\Api\V1\Doctor\DoctorService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function __construct(
        protected TimeSlotRepositoryInterface $timeSlotRepository,
        protected DoctorService $doctorService
    ) {}

    /**
     * List all weekly schedule patterns for the logged-in doctor.
     */
    public function index(Request $request): JsonResponse
    {
        $doctor = $this->doctorService->getProfileByUserId($request->user()->id);

        if (! $doctor) {
            return response()->json(['message' => 'Doctor profile not found'], 404);
        }

        $slots = $this->timeSlotRepository->getByDoctorId($doctor->id);

        // Group by day for better readability
        $grouped = $slots->groupBy('day_of_week');

        return response()->json([
            'data' => $grouped,
        ]);
    }

    /**
     * bulk-create slots based on a time range.
     * Example: Saturday, 09:00 to 12:00, 30 mins each.
     */
    public function store(StoreScheduleRequest $request): JsonResponse
    {
        $doctor = $this->doctorService->getProfileByUserId($request->user()->id);

        if (! $doctor) {
            return response()->json(['message' => 'Doctor profile not found'], 404);
        }

        $startTime = Carbon::createFromFormat('H:i', $request->start_time);
        $endTime = Carbon::createFromFormat('H:i', $request->end_time);
        $duration = $request->duration_minutes;

        $createdSlots = [];
        $current = $startTime->copy();

        while ($current->copy()->addMinutes($duration)->lte($endTime)) {
            $slotStart = $current->format('H:i:s');
            $current->addMinutes($duration);
            $slotEnd = $current->format('H:i:s');

            $createdSlots[] = $this->timeSlotRepository->create([
                'doctor_id' => $doctor->id,
                'day_of_week' => $request->day_of_week,
                'start_time' => $slotStart,
                'end_time' => $slotEnd,
                'duration_minutes' => $duration,
                'is_available' => true,
            ]);
        }

        return response()->json([
            'message' => count($createdSlots).' slots created successfully.',
            'data' => $createdSlots,
        ], 201);
    }

    /**
     * Delete a specific slot.
     */
    public function destroy(string $id, Request $request): JsonResponse
    {
        $doctor = $this->doctorService->getProfileByUserId($request->user()->id);
        $slot = $this->timeSlotRepository->find($id);

        if (! $slot || $slot->doctor_id !== $doctor->id) {
            return response()->json(['message' => 'Slot not found or unauthorized'], 404);
        }

        $this->timeSlotRepository->delete($id);

        return response()->json(null, 204);
    }

    /**
     * Clear all slots for a specific day.
     */
    public function clearDay(string $day, Request $request): JsonResponse
    {
        $doctor = $this->doctorService->getProfileByUserId($request->user()->id);

        TimeSlot::where('doctor_id', $doctor->id)
            ->where('day_of_week', $day)
            ->delete();

        return response()->json(['message' => "All slots for $day cleared."]);
    }
}
