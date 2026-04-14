<?php

use App\Http\Controllers\Api\V1\Admin\DoctorController;
use App\Http\Controllers\Api\V1\Admin\SpecialtyController;
use App\Http\Controllers\Api\V1\Admin\UserController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\SocialAuthController;
use App\Http\Controllers\Api\V1\Doctor\DashboardController;
use App\Http\Controllers\Api\V1\Doctor\ProfileController;
use App\Http\Controllers\Api\V1\Doctor\ScheduleController;
use App\Http\Controllers\Api\V1\Patient\AppointmentController;
use App\Http\Controllers\Api\V1\Patient\PaymentController;
use App\Http\Controllers\Api\V1\Patient\ReviewController;
use App\Http\Controllers\Api\V1\Webhook\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Public Auth Routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/social-login', [SocialAuthController::class, 'handleSocialLogin']);

    // Public Webhook Routes
    Route::prefix('webhooks')->group(function () {
        Route::post('/paymob', [PaymentWebhookController::class, 'paymob']);
        Route::post('/stripe', [PaymentWebhookController::class, 'stripe']);
    });

    // Protected Routes
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::match(['put', 'patch'], '/profile', [AuthController::class, 'updateProfile']);

        // --- Doctor Routes ---
        Route::prefix('doctor')->middleware('role:doctor')->group(function () {
            Route::get('/profile', [ProfileController::class, 'index']);
            Route::match(['put', 'patch'], '/profile', [ProfileController::class, 'update']);

            Route::get('/appointments', [AppointmentController::class, 'index']);
            Route::get('/appointments/{id}', [AppointmentController::class, 'show']);
            Route::patch('/appointments/{id}/status', [AppointmentController::class, 'updateStatus']);

            // Schedule Management
            Route::get('/schedule', [ScheduleController::class, 'index']);
            Route::post('/schedule', [ScheduleController::class, 'store']);
            Route::delete('/schedule/{id}', [ScheduleController::class, 'destroy']);
            Route::delete('/schedule/clear/{day}', [ScheduleController::class, 'clearDay']);

            Route::get('/dashboard', [DashboardController::class, 'index']);
        });

        // --- Patient Routes ---
        Route::prefix('patient')->middleware('role:patient')->group(function () {
            Route::get('/doctors', [DoctorController::class, 'index']);
            Route::get('/doctors/{id}', [DoctorController::class, 'show']);
            Route::get('/doctors/{id}/availability', [DoctorController::class, 'availableSlots']);

            Route::get('/appointments', [AppointmentController::class, 'index']);
            Route::post('/appointments', [AppointmentController::class, 'store']);
            Route::get('/appointments/{id}', [AppointmentController::class, 'show']);
            Route::post('/appointments/{id}/cancel', [AppointmentController::class, 'cancel']);

            Route::post('/reviews', [ReviewController::class, 'store']);

            // Payments
            Route::post('/payments/initiate', [PaymentController::class, 'initiate']);
        });

        // --- Admin Routes ---
        Route::prefix('admin')->middleware('role:admin')->group(function () {
            Route::apiResource('users', UserController::class);
            Route::apiResource('doctors', DoctorController::class);
            Route::apiResource('specialties', SpecialtyController::class);
            Route::apiResource('appointments', AppointmentController::class);

            Route::get('/reviews', [ReviewController::class, 'index']);
            Route::post('/reviews/{id}/approve', [ReviewController::class, 'approve']);
            Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

            Route::get('/dashboard', [App\Http\Controllers\Api\V1\Admin\DashboardController::class, 'index']);
        });
    });
});
