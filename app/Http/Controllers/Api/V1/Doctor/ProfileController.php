<?php

namespace App\Http\Controllers\Api\V1\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Doctor\UpdateDoctorProfileRequest;
use App\Http\Resources\Api\V1\Doctor\DoctorResource;
use App\Services\Api\V1\Doctor\DoctorService;
use App\Services\Api\V1\Translation\TranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        protected DoctorService $doctorService,
        protected TranslationService $translationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $doctor = $this->doctorService->getProfileByUserId($request->user()->id);

        if (! $doctor) {
            return response()->json(['message' => 'Doctor profile not found'], 404);
        }

        return response()->json(new DoctorResource($doctor));
    }

    public function update(UpdateDoctorProfileRequest $request): JsonResponse
    {
        $doctor = $this->doctorService->getProfileByUserId($request->user()->id);

        if (! $doctor) {
            return response()->json(['message' => 'Doctor profile not found'], 404);
        }

        $data = $request->validated();
        $translations = $data['translations'] ?? [];
        unset($data['translations']);

        $updatedDoctor = $this->doctorService->updateProfile($doctor, $data);

        if (! empty($translations)) {
            $this->translationService->sync($updatedDoctor, $translations);
        }

        return response()->json([
            'message' => 'Doctor profile updated successfully',
            'data' => new DoctorResource($updatedDoctor),
        ]);
    }
}
