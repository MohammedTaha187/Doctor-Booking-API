<?php

namespace App\Http\Controllers\Api\V1\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Payment\InitiatePaymentRequest;
use App\Models\Appointment;
use App\Services\Api\V1\Payment\PaymentService;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}

    /**
     * Initiate payment for an appointment.
     */
    public function initiate(InitiatePaymentRequest $request): JsonResponse
    {
        $appointment = Appointment::with('doctor')->findOrFail($request->appointment_id);

        // Ensure the authenticated user owns this appointment
        if ($appointment->patient_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($appointment->payment_status === 'paid') {
            return response()->json(['message' => 'Appointment is already paid'], 422);
        }

        if ($appointment->status === 'cancelled') {
            return response()->json(['message' => 'Cannot pay for a cancelled appointment'], 422);
        }

        $result = $this->paymentService
            ->gateway($request->gateway)
            ->initiate(
                $request->user(),
                $appointment,
                $appointment->doctor->consultation_fee
            );

        return response()->json([
            'message' => 'Payment initiated. Redirect user to the provided URL.',
            'data' => $result,
        ]);
    }
}
