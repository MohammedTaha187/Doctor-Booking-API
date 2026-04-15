<?php

namespace App\Http\Controllers\Api\V1\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Appointment\StoreBookingRequest;
use App\Http\Resources\Api\V1\Appointment\AppointmentResource;
use App\Models\TimeSlot;
use App\Services\Api\V1\Appointment\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(protected AppointmentService $appointmentService) {}

    /**
     * List current patient's appointments.
     */
    public function index(Request $request): JsonResponse
    {
        $appointments = $this->appointmentService->getForPatient($request->user()->id);

        return response()->json(AppointmentResource::collection($appointments)->response()->getData(true));
    }

    /**
     * Show a specific appointment details.
     */
    public function show(string $id, Request $request): JsonResponse
    {
        $appointment = $this->appointmentService->find($id);

        if (! $appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        $this->authorize('view', $appointment);

        return response()->json(new AppointmentResource($appointment));
    }

    /**
     * Book a new appointment.
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        $slot = TimeSlot::find($request->time_slot_id);

        if (! $slot) {
            return response()->json(['message' => 'Time slot not found'], 404);
        }

        if ((string) $slot->doctor_id !== (string) $request->doctor_id) {
            return response()->json(['message' => 'Time slot does not belong to the selected doctor'], 422);
        }

        try {
            $data = $request->validated();
            $data['patient_id'] = $request->user()->id;
            $data['scheduled_time'] = $slot->start_time; // Automatically get time from slot
            $data['status'] = 'pending';

            $appointment = $this->appointmentService->bookAppointment($data);

            return response()->json([
                'message' => 'Appointment booked successfully. Waiting for confirmation.',
                'data' => new AppointmentResource($appointment),
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Cancel an appointment.
     */
    public function cancel(string $id, Request $request): JsonResponse
    {
        $appointment = $this->appointmentService->find($id);

        if (! $appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        $this->authorize('cancel', $appointment);

        $this->appointmentService->updateStatus($id, 'cancelled');

        return response()->json(['message' => 'Appointment cancelled successfully.']);
    }
}
