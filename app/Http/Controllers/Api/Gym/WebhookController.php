<?php

namespace App\Http\Controllers\Api\Gym;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Services\AbacatePayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(private AbacatePayService $abacatePay) {}

    public function handle(Request $request): JsonResponse
    {
        $secret = $request->header('X-Abacatepay-Secret', '');
        if (! $this->abacatePay->validateWebhookSecret($secret)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $event = $request->input('event');
        $billingId = $request->input('data.id');

        if (! $billingId) {
            return response()->json(['message' => 'ok']);
        }

        $subscription = Subscription::where('abacatepay_billing_id', $billingId)->first();

        if (! $subscription) {
            return response()->json(['message' => 'ok']);
        }

        match ($event) {
            'BILLING_PAID' => $this->handlePaid($subscription),
            'BILLING_EXPIRED', 'BILLING_CANCELLED' => $this->handleCancelled($subscription, $event),
            default => null,
        };

        return response()->json(['message' => 'ok']);
    }

    private function handlePaid(Subscription $subscription): void
    {
        $subscription->update([
            'status'               => 'active',
            'current_period_start' => now(),
            'current_period_end'   => now()->addMonth(),
        ]);
    }

    private function handleCancelled(Subscription $subscription, string $event): void
    {
        $status = $event === 'BILLING_EXPIRED' ? 'expired' : 'cancelled';
        $subscription->update(['status' => $status]);
    }
}
