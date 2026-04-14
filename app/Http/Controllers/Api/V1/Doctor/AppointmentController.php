<?php

namespace App\Http\Controllers\Api\V1\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Appointment\AppointmentResource;
use App\Services\Api\V1\Appointment\AppointmentService;
use App\Services\Api\V1\Doctor\DoctorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(
        protected AppointmentService $appointmentService,
        protected DoctorService $doctorService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $doctor = $this->doctorService->getProfileByUserId($request->user()->id);

        if (! $doctor) {
            return response()->json(['message' => 'Doctor profile not found'], 404);
        }

        $appointments = $this->appointmentService->getForDoctor($doctor->id);

        return response()->json(AppointmentResource::collection($appointments)->response()->getData(true));
    }

    public function show(string $id, Request $request): JsonResponse
    {
        $appointment = $this->appointmentService->appointmentRepository->find($id);

        if (! $appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        $this->authorize('view', $appointment);

        return response()->json(new AppointmentResource($appointment));
    }

    public function updateStatus(string $id, Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:confirmed,cancelled,completed,no_show',
        ]);

        $appointment = $this->appointmentService->appointmentRepository->find($id);

        if (! $appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        // Apply specific policy based on target status
        $policyAction = match ($request->status) {
            'confirmed' => 'confirm',
            'completed' => 'complete',
            default => 'view', // 'view' checks ownership, generic enough for simple status changes if not confirmed/completed
        };

        $this->authorize($policyAction, $appointment);

        $appointment = $this->appointmentService->updateStatus($id, $request->status);

        return response()->json([
            'message' => "Appointment status updated to {$request->status}",
            'data' => new AppointmentResource($appointment),
        ]);
    }
}
