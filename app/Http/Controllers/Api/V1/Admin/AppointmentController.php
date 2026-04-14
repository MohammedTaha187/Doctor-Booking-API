<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Api\V1\Appointment\UpdateAppointmentRequest;
use App\Http\Resources\Api\V1\Appointment\AppointmentResource;
use App\Repositories\Interfaces\AppointmentRepositoryInterface;
use Illuminate\Http\JsonResponse;

class AppointmentController extends Controller
{
    public function __construct(protected AppointmentRepositoryInterface $appointmentRepository) {}

    public function index(): JsonResponse
    {
        return response()->json(AppointmentResource::collection($this->appointmentRepository->all())->response()->getData(true));
    }

    public function show(string $id): JsonResponse
    {
        $appointment = $this->appointmentRepository->find($id);
        if (! $appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        return response()->json(new AppointmentResource($appointment));
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $appointment = $this->appointmentRepository->create($request->validated());

        return response()->json(new AppointmentResource($appointment), 201);
    }

    public function update(UpdateAppointmentRequest $request, string $id): JsonResponse
    {
        $appointment = $this->appointmentRepository->update($id, $request->validated());
        if (! $appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        return response()->json(new AppointmentResource($appointment));
    }

    public function destroy(string $id): JsonResponse
    {
        if (! $this->appointmentRepository->delete($id)) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        return response()->json(null, 204);
    }
}
