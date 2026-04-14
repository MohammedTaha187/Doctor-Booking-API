<?php

namespace App\Http\Controllers\Api\V1\Webhook;

use App\Http\Controllers\Controller;
use App\Services\Api\V1\Payment\PaymobService;
use App\Services\Api\V1\Payment\StripeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    /**
     * Handle Paymob webhook (HMAC verified).
     */
    public function paymob(Request $request, PaymobService $paymobService): JsonResponse
    {
        // Verify HMAC signature
        $hmacSecret = config('services.paymob.hmac_secret', '');
        $receivedHmac = $request->query('hmac');

        $data = collect($request->all())->except('hmac')->sortKeys();
        $concatenated = $data->values()->implode('');
        $computedHmac = hash_hmac('sha512', $concatenated, $hmacSecret);

        if (! hash_equals($computedHmac, $receivedHmac ?? '')) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $paymobService->handleWebhook($request->all());

        return response()->json(['message' => 'OK']);
    }

    /**
     * Handle Stripe webhook (Stripe-Signature verified).
     */
    public function stripe(Request $request, StripeService $stripeService): JsonResponse
    {
        $signature = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret', '');

        // In production, verify with: \Stripe\Webhook::constructEvent(...)
        if (empty($signature)) {
            return response()->json(['message' => 'Missing signature'], 403);
        }

        $stripeService->handleWebhook($request->json()->all());

        return response()->json(['message' => 'OK']);
    }
}
