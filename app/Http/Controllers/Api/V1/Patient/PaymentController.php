<?php

namespace App\Http\Controllers\Api\V1\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Payment\InitiatePaymentRequest;
use App\Models\Appointment;
use App\Services\Api\V1\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}

    /**
     * Initiate payment for an appointment.
     */
    public function initiate(InitiatePaymentRequest $request): JsonResponse
    {
        $appointment = Appointment::with('doctor')->findOrFail($request->appointment_id);

        $this->authorize('pay', $appointment);

        if (! $appointment->doctor || $appointment->doctor->consultation_fee === null) {
            return response()->json([
                'message' => 'Consultation fee is not configured for this doctor.',
            ], 422);
        }

        try {
            $result = $this->paymentService
                ->gateway($request->gateway)
                ->initiate(
                    $request->user(),
                    $appointment,
                    (float) $appointment->doctor->consultation_fee
                );
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Payment gateway temporarily unavailable. Please try again later.',
            ], 502);
        }

        return response()->json([
            'message' => 'Payment initiated. Redirect user to the provided URL.',
            'data' => $result,
        ]);
    }
}
