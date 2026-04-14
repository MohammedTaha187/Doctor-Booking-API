<?php

namespace App\Services\Api\V1\Auth;

use App\Events\UserRegistered;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(protected UserRepositoryInterface $userRepository) {}

    public function register(array $data): array
    {
        $user = $this->userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'avatar' => $data['avatar'] ?? null,
            'phone' => $data['phone'] ?? null,
            'gender' => $data['gender'] ?? 'male',
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'language_preference' => $data['language_preference'] ?? 'ar',
        ]);

        $user->assignRole('patient');

        event(new UserRegistered($user));

        $token = auth('api')->login($user);

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }

    public function login(array $data): array
    {
        $credentials = ['email' => $data['email'], 'password' => $data['password']];

        if (! $token = auth('api')->attempt($credentials)) {
            throw new \Exception('Invalid credentials');
        }

        return [
            'user' => auth('api')->user(),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }

    public function logout(User $user): void
    {
        auth('api')->logout();
    }

    public function refresh(User $user): array
    {
        $token = auth('api')->refresh();

        return [
            'user' => auth('api')->user(),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }

    public function changePassword(User $user, array $data): void
    {
        if (! Hash::check($data['current_password'], $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        $this->userRepository->update($user->id, [
            'password' => Hash::make($data['new_password']),
        ]);
    }

    public function forgotPassword(array $data): void
    {
        $user = $this->userRepository->findByEmail($data['email']);
        if (! $user) {
            throw new \Exception('User not found');
        }

        // @todo Generate token and send email logic
    }

    public function resetPassword(array $data): void
    {
        $user = $this->userRepository->findByEmail($data['email'] ?? '');

        if (! $user) {
            throw new \Exception('User not found');
        }

        $this->userRepository->update($user->id, [
            'password' => Hash::make($data['password']),
        ]);
    }

    public function updateProfile(User $user, array $data): void
    {
        $this->userRepository->update($user->id, $data);
    }
}
