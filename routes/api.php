<?php

use App\Http\Controllers\Api\V1\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Api\V1\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\V1\Admin\DoctorController as AdminDoctorController;
use App\Http\Controllers\Api\V1\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Api\V1\Admin\SpecialtyController;
use App\Http\Controllers\Api\V1\Admin\UserController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\SocialAuthController;
use App\Http\Controllers\Api\V1\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\Api\V1\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Api\V1\Doctor\ProfileController as DoctorProfileController;
use App\Http\Controllers\Api\V1\Doctor\ScheduleController;
use App\Http\Controllers\Api\V1\Patient\AppointmentController as PatientAppointmentController;
use App\Http\Controllers\Api\V1\Patient\DoctorController as PatientDoctorController;
use App\Http\Controllers\Api\V1\Patient\PaymentController;
use App\Http\Controllers\Api\V1\Patient\ReviewController as PatientReviewController;
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
        Route::post('/kashier', [PaymentWebhookController::class, 'kashier']);
        Route::post('/paypal', [PaymentWebhookController::class, 'paypal']);
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
            Route::get('/profile', [DoctorProfileController::class, 'index']);
            Route::match(['put', 'patch'], '/profile', [DoctorProfileController::class, 'update']);

            Route::get('/appointments', [DoctorAppointmentController::class, 'index']);
            Route::get('/appointments/{id}', [DoctorAppointmentController::class, 'show']);
            Route::patch('/appointments/{id}/status', [DoctorAppointmentController::class, 'updateStatus']);

            // Schedule Management
            Route::get('/schedule', [ScheduleController::class, 'index']);
            Route::post('/schedule', [ScheduleController::class, 'store']);
            Route::delete('/schedule/{id}', [ScheduleController::class, 'destroy']);
            Route::delete('/schedule/clear/{day}', [ScheduleController::class, 'clearDay']);

            Route::get('/dashboard', [DoctorDashboardController::class, 'index']);
        });

        // --- Patient Routes ---
        Route::prefix('patient')->middleware('role:patient')->group(function () {
            Route::get('/doctors', [PatientDoctorController::class, 'index']);
            Route::get('/doctors/{id}', [PatientDoctorController::class, 'show']);
            Route::get('/doctors/{id}/availability', [PatientDoctorController::class, 'availableSlots']);

            Route::get('/appointments', [PatientAppointmentController::class, 'index']);
            Route::post('/appointments', [PatientAppointmentController::class, 'store']);
            Route::get('/appointments/{id}', [PatientAppointmentController::class, 'show']);
            Route::post('/appointments/{id}/cancel', [PatientAppointmentController::class, 'cancel']);

            Route::post('/reviews', [PatientReviewController::class, 'store']);

            // Payments
            Route::post('/payments/initiate', [PaymentController::class, 'initiate']);
        });

        // --- Admin Routes ---
        Route::prefix('admin')->middleware('role:admin')->group(function () {
            Route::apiResource('users', UserController::class);
            Route::apiResource('doctors', AdminDoctorController::class);
            Route::apiResource('specialties', SpecialtyController::class);
            Route::apiResource('appointments', AdminAppointmentController::class);

            Route::get('/reviews', [AdminReviewController::class, 'index']);
            Route::post('/reviews/{id}/approve', [AdminReviewController::class, 'approve']);
            Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy']);

            Route::get('/dashboard', [AdminDashboardController::class, 'index']);
        });
    });
});
