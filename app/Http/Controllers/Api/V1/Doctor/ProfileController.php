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
            return response()->json([
                'message' => 'Doctor profile is incomplete. Please update your profile.',
                'status' => 'incomplete',
            ], 200);
        }

        return response()->json(new DoctorResource($doctor));
    }

    public function update(UpdateDoctorProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $doctor = $this->doctorService->getProfileByUserId($user->id);

        $data = $request->validated();
        $translations = $data['translations'] ?? [];
        unset($data['translations']);

        if (! $doctor) {
            // Initialize profile if it doesn't exist
            $data['user_id'] = $user->id;
            // Ensure minimum required fields for a new record or use defaults
            $data['license_number'] = $data['license_number'] ?? 'PENDING-'.time();
            $data['years_experience'] = $data['years_experience'] ?? 0;
            $data['specialty_id'] = $data['specialty_id'] ?? null;

            $updatedDoctor = $this->doctorService->createProfile($data);
        } else {
            $updatedDoctor = $this->doctorService->updateProfile($doctor, $data);
        }

        if (! empty($translations)) {
            $this->translationService->sync($updatedDoctor, $translations);
        }

        return response()->json([
            'message' => 'Doctor profile updated successfully',
            'data' => new DoctorResource($updatedDoctor->fresh()),
        ]);
    }
}
