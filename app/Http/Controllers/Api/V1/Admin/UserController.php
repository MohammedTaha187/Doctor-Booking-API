<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\StoreUserRequest;
use App\Http\Requests\Api\V1\User\UpdateUserRequest;
use App\Http\Resources\Api\V1\Auth\UserResource;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(protected UserRepositoryInterface $userRepository) {}

    public function index(): JsonResponse
    {
        return response()->json(UserResource::collection($this->userRepository->all()));
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userRepository->create($request->validated());

        return response()->json(new UserResource($user), 201);
    }

    public function show(string $id): JsonResponse
    {
        $user = $this->userRepository->find($id);
        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(new UserResource($user));
    }

    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $user = $this->userRepository->update($id, $request->validated());
        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(new UserResource($user));
    }

    public function destroy(string $id): JsonResponse
    {
        if (! $this->userRepository->delete($id)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(null, 204);
    }
}
