<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Doctor\StoreDoctorRequest;
use App\Http\Requests\Api\V1\Doctor\UpdateDoctorRequest;
use App\Http\Resources\Api\V1\Doctor\DoctorResource;
use App\Repositories\Interfaces\DoctorRepositoryInterface;
use Illuminate\Http\JsonResponse;

class DoctorController extends Controller
{
    public function __construct(protected DoctorRepositoryInterface $doctorRepository) {}

    public function index(): JsonResponse
    {
        return response()->json(DoctorResource::collection($this->doctorRepository->all()));
    }

    public function store(StoreDoctorRequest $request): JsonResponse
    {
        $doctor = $this->doctorRepository->create($request->validated());

        return response()->json(new DoctorResource($doctor), 201);
    }

    public function show(string $id): JsonResponse
    {
        $doctor = $this->doctorRepository->find($id);
        if (! $doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        return response()->json(new DoctorResource($doctor));
    }

    public function update(UpdateDoctorRequest $request, string $id): JsonResponse
    {
        $doctor = $this->doctorRepository->update($id, $request->validated());
        if (! $doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        return response()->json(new DoctorResource($doctor));
    }

    public function destroy(string $id): JsonResponse
    {
        if (! $this->doctorRepository->delete($id)) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        return response()->json(null, 204);
    }
}
