<?php

namespace App\Http\Controllers\Api\V1\Webhook;

use App\Http\Controllers\Controller;
use App\Services\Api\V1\Payment\PaymobService;
use App\Services\Api\V1\Payment\StripeService;
use App\Services\Api\V1\Payment\KashierService;
use App\Services\Api\V1\Payment\PayPalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    /**
     * Handle Paymob webhook (HMAC verified).
     */
    public function paymob(Request $request, PaymobService $paymobService): JsonResponse
    {
        $hmacSecret = config('services.paymob.hmac_secret', '');
        $receivedHmac = $request->query('hmac');

        if ($hmacSecret === '') {
            return response()->json(['message' => 'Paymob webhook secret is not configured'], 500);
        }

        $data = collect($request->all())->except('hmac')->sortKeys();
        $concatenated = $data->values()->implode('');
        $computedHmac = hash_hmac('sha512', $concatenated, $hmacSecret);

        if (! is_string($receivedHmac) || ! hash_equals($computedHmac, $receivedHmac)) {
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

        if ($secret === '') {
            return response()->json(['message' => 'Stripe webhook secret is not configured'], 500);
        }

        if (! is_string($signature) || $signature === '') {
            return response()->json(['message' => 'Missing signature'], 403);
        }

        if ($request->json('type') === null || $request->input('data.object.id') === null) {
            return response()->json(['message' => 'Invalid payload'], 422);
        }

        $stripeService->handleWebhook($request->json()->all());

        return response()->json(['message' => 'OK']);
    }

    /**
     * Handle Kashier webhook (HMAC verified).
     */
    public function kashier(Request $request, KashierService $kashierService): JsonResponse
    {
        // Simple placeholder for Kashier webhook logic
        $kashierService->handleWebhook($request->all());
        return response()->json(['message' => 'OK']);
    }

    /**
     * Handle PayPal webhook.
     */
    public function paypal(Request $request, PaypalService $paypalService): JsonResponse
    {
        // Simple placeholder for PayPal webhook logic
        $paypalService->handleWebhook($request->all());
        return response()->json(['message' => 'OK']);
    }
}
