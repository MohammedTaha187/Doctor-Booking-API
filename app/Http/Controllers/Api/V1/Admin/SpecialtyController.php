<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Specialty\StoreSpecialtyRequest;
use App\Http\Requests\Api\V1\Specialty\UpdateSpecialtyRequest;
use App\Http\Resources\Api\V1\Specialty\SpecialtyResource;
use App\Repositories\Interfaces\SpecialtyRepositoryInterface;
use App\Services\Api\V1\Translation\TranslationService;
use Illuminate\Http\JsonResponse;

class SpecialtyController extends Controller
{
    public function __construct(
        protected SpecialtyRepositoryInterface $specialtyRepository,
        protected TranslationService $translationService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json(SpecialtyResource::collection($this->specialtyRepository->all()));
    }

    public function store(StoreSpecialtyRequest $request): JsonResponse
    {
        $data = $request->validated();
        $translations = $data['translations'] ?? [];
        unset($data['translations']);

        $specialty = $this->specialtyRepository->create($data);

        if (! empty($translations)) {
            $this->translationService->sync($specialty, $translations);
        }

        return response()->json(new SpecialtyResource($specialty), 201);
    }

    public function show(string $id): JsonResponse
    {
        $specialty = $this->specialtyRepository->find($id);
        if (! $specialty) {
            return response()->json(['message' => 'Specialty not found'], 404);
        }

        return response()->json(new SpecialtyResource($specialty));
    }

    public function update(UpdateSpecialtyRequest $request, string $id): JsonResponse
    {
        $data = $request->validated();
        $translations = $data['translations'] ?? [];
        unset($data['translations']);

        $specialty = $this->specialtyRepository->update($id, $data);
        if (! $specialty) {
            return response()->json(['message' => 'Specialty not found'], 404);
        }

        if (! empty($translations)) {
            $this->translationService->sync($specialty, $translations);
        }

        return response()->json(new SpecialtyResource($specialty));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->specialtyRepository->delete($id);

        return response()->json(null, 204);
    }
}
