<?php

namespace App\Http\Controllers\Api\V1\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Appointment\AppointmentResource;
use App\Services\Api\V1\Appointment\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(protected AppointmentService $appointmentService) {}

    /**
     * List appointments for the current doctor.
     */
    public function index(Request $request): JsonResponse
    {
        $appointments = $this->appointmentService->getForDoctor($request->user()->doctor->id);

        return response()->json(AppointmentResource::collection($appointments)->response()->getData(true));
    }

    /**
     * View details of a specific appointment.
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
     * Update appointment status (confirm, complete, etc.).
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $request->validate(['status' => 'required|in:confirmed,completed,no_show,cancelled']);

        $appointment = $this->appointmentService->find($id);

        if (! $appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        $this->authorize('updateStatus', $appointment);

        $appointment = $this->appointmentService->updateStatus($id, $request->status);

        return response()->json([
            'message' => "Appointment status updated to {$request->status}.",
            'data' => new AppointmentResource($appointment),
        ]);
    }
}
