<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
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

    public function show(string $id): JsonResponse
    {
        $user = $this->userRepository->find($id);
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
