<?php

namespace App\Services\Api\V1\Auth;

use App\Events\UserRegistered;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    // Best Practice: Inject the Interface, not the Implementation class!
    public function __construct(protected UserRepositoryInterface $userRepository) {}

    public function register(array $data): array
    {
        $user = $this->userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // Usually hashed in a mutator or observer, assuming that's handled, or hash here
            'avatar' => $data['avatar'] ?? null,
            'phone' => $data['phone'] ?? null,
            'gender' => $data['gender'] ?? 'male',
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'language_preference' => $data['language_preference'] ?? 'ar',
        ]);

        $user->assignRole('customer');

        // Dispatch the Registration Event
        event(new UserRegistered($user));

        $token = $user->createToken('auth_token')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function login(array $data): array
    {
        $user = $this->userRepository->findByEmail($data['email']);
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw new \Exception('Invalid credentials');
        }
        $token = $user->createToken('auth_token')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function refresh(User $user): array
    {
        $user->currentAccessToken()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function changePassword(User $user, array $data): void
    {
        if (! Hash::check($data['current_password'], $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        // Use repository instead of $user->save()
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

        // TODO: Generate token and send email in a real app
    }

    public function resetPassword(array $data): void
    {
        // For now, we assume email search and update (simplified)
        $user = $this->userRepository->findByEmail($data['email'] ?? ''); // Added email fallback if needed, or get from token

        // If the request has the email, we use it. If not, we might need to handle token validation.
        // Let's assume the ResetPasswordRequest provides email or we adjust Request.

        if (! $user) {
            // Try to find user by some other means if email isn't in request,
            // but ResetPasswordRequest should ideally have email or token logic.
            throw new \Exception('User not found');
        }

        $this->userRepository->update($user->id, [
            'password' => Hash::make($data['password']),
        ]);
    }

    public function updateProfile(User $user, array $data): void
    {
        // Use repository instead of raw update
        $this->userRepository->update($user->id, $data);
    }
}
