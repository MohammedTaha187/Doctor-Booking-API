<?php

namespace App\Services\Api\V1\Auth;

use App\Events\UserRegistered;
use App\Repositories\Interfaces\UserRepositoryInterface;

class SocialAuthService
{
    // Best Practice: Inject Interface
    public function __construct(protected UserRepositoryInterface $userRepository) {}

    /**
     * Handle social login (Google, Facebook, Apple, etc.)
     * This acts as both Register and Login.
     */
    public function handleSocialLogin(array $data): array
    {
        // 1. Check if user already registered via this social provider
        $user = $this->userRepository->findByProvider($data['social_provider'], $data['social_id']);

        if (! $user) {
            // 2. Fallback: check if the user exists with the same email (e.g. standard login before)
            $user = $this->userRepository->findByEmail($data['email']);

            if ($user) {
                // Link the social account to existing user
                $this->userRepository->update($user->id, [
                    'social_provider' => $data['social_provider'],
                    'social_id' => $data['social_id'],
                ]);
            } else {
                // 3. Create a brand new user (No password needed for social login)
                $user = $this->userRepository->create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => null,
                    'avatar' => $data['avatar'] ?? null,
                    'social_provider' => $data['social_provider'],
                    'social_id' => $data['social_id'],
                    'gender' => $data['gender'] ?? 'male',
                    'language_preference' => $data['language_preference'] ?? 'ar',
                ]);

                $user->assignRole('customer');

                // Fire Registration Event
                event(new UserRegistered($user));
            }
        }

        // Generate Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }
}
