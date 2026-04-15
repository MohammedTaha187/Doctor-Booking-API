<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Payment;
use App\Models\Specialty;
use App\Models\TimeSlot;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    /**
     * Test patient can initiate a payment.
     */
    public function test_patient_can_initiate_payment(): void
    {
        $patient = $this->actingAsPatient();

        $specialty = Specialty::factory()->create();
        $doctorUser = $this->createUserWithRole('doctor');
        $doctor = Doctor::factory()->create(['user_id' => $doctorUser->id, 'specialty_id' => $specialty->id, 'consultation_fee' => 100]);
        $slot = TimeSlot::factory()->create(['doctor_id' => $doctor->id]);

        $appointment = Appointment::factory()->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'time_slot_id' => $slot->id,
        ]);

        $response = $this->postJson('/api/v1/patient/payments/initiate', [
            'appointment_id' => $appointment->id,
            'gateway' => 'stripe',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'data' => ['url', 'order_id']]);

        $this->assertDatabaseHas('payments', [
            'appointment_id' => $appointment->id,
            'gateway' => 'stripe',
            'status' => 'pending',
        ]);
    }

    /**
     * Test patient can initiate a Paymob payment when the gateway responds successfully.
     */
    public function test_patient_can_initiate_paymob_payment(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://accept.paymob.com/api/auth/tokens' => Http::response(['token' => 'paymob-token'], 200),
            'https://accept.paymob.com/api/ecommerce/orders' => Http::response(['id' => 123456], 200),
            'https://accept.paymob.com/api/acceptance/payment_keys' => Http::response(['token' => 'payment-key'], 200),
        ]);

        config([
            'services.paymob.api_key' => 'test_api_key',
            'services.paymob.integration_id' => '123',
            'services.paymob.iframe_id' => '456',
        ]);

        $patient = $this->actingAsPatient();

        $specialty = Specialty::factory()->create();
        $doctorUser = $this->createUserWithRole('doctor');
        $doctor = Doctor::factory()->create([
            'user_id' => $doctorUser->id,
            'specialty_id' => $specialty->id,
            'consultation_fee' => 250,
        ]);
        $slot = TimeSlot::factory()->create(['doctor_id' => $doctor->id]);

        $appointment = Appointment::factory()->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'time_slot_id' => $slot->id,
        ]);

        $response = $this->postJson('/api/v1/patient/payments/initiate', [
            'appointment_id' => $appointment->id,
            'gateway' => 'paymob',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.order_id', '123456')
            ->assertJsonPath('data.url', 'https://accept.paymob.com/api/acceptance/iframes/456?payment_token=payment-key');

        $this->assertDatabaseHas('payments', [
            'appointment_id' => $appointment->id,
            'gateway' => 'paymob',
            'gateway_order_id' => '123456',
            'status' => 'pending',
        ]);

        Http::assertSentCount(3);
    }

    /**
     * Test patient gets a graceful failure when Paymob responds with an error.
     */
    public function test_patient_gets_gateway_error_when_paymob_fails(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://accept.paymob.com/api/auth/tokens' => Http::response(['message' => 'error'], 500),
        ]);

        config([
            'services.paymob.api_key' => 'test_api_key',
            'services.paymob.integration_id' => '123',
            'services.paymob.iframe_id' => '456',
        ]);

        $patient = $this->actingAsPatient();

        $specialty = Specialty::factory()->create();
        $doctorUser = $this->createUserWithRole('doctor');
        $doctor = Doctor::factory()->create([
            'user_id' => $doctorUser->id,
            'specialty_id' => $specialty->id,
            'consultation_fee' => 250,
        ]);
        $slot = TimeSlot::factory()->create(['doctor_id' => $doctor->id]);

        $appointment = Appointment::factory()->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'time_slot_id' => $slot->id,
        ]);

        $response = $this->postJson('/api/v1/patient/payments/initiate', [
            'appointment_id' => $appointment->id,
            'gateway' => 'paymob',
        ]);

        $response->assertStatus(502)
            ->assertJson([
                'message' => 'Payment gateway temporarily unavailable. Please try again later.',
            ]);
    }

    /**
     * Test Paymob webhook updates payment status.
     */
    public function test_paymob_webhook_rejects_invalid_signature(): void
    {
        $appointment = Appointment::factory()->create();
        $payment = Payment::create([
            'user_id' => $appointment->patient_id,
            'appointment_id' => $appointment->id,
            'gateway' => 'paymob',
            'gateway_order_id' => '123456',
            'amount' => 100,
            'currency' => 'EGP',
            'status' => 'pending',
        ]);

        config(['services.paymob.hmac_secret' => 'test_secret']);

        $payload = [
            'obj' => [
                'success' => true,
                'id' => 'TRANS_789',
                'order' => ['id' => '123456'],
            ],
        ];

        $response = $this->postJson('/api/v1/webhooks/paymob?hmac=INVALID', $payload);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Invalid signature',
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'pending',
        ]);
    }
}
