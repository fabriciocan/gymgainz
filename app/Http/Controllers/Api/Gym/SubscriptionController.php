<?php

namespace App\Http\Controllers\Api\Gym;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use App\Services\AbacatePayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(private AbacatePayService $abacatePay) {}

    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        $subscription = $user->subscriptions()->latest()->first();

        return response()->json([
            'has_access'   => $user->hasActiveAccess(),
            'trial_ends_at'=> $user->trial_ends_at?->toDateTimeString(),
            'subscription' => $subscription ? new SubscriptionResource($subscription) : null,
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'cellphone' => 'required|string',
            'tax_id'    => 'required|string',
        ]);

        $user = $request->user();

        $existing = $user->subscriptions()->where('status', 'active')->first();
        if ($existing) {
            return response()->json(['message' => 'Você já possui uma assinatura ativa.'], 422);
        }

        $billing = $this->abacatePay->createBilling([
            'name'      => $user->name,
            'email'     => $user->email,
            'cellphone' => $request->cellphone,
            'taxId'     => $request->tax_id,
        ]);

        $subscription = Subscription::create([
            'user_id'              => $user->id,
            'status'               => 'active',
            'plan'                 => 'basic',
            'amount_cents'         => 1990,
            'abacatepay_billing_id'=> $billing['data']['id'] ?? null,
            'current_period_start' => now(),
            'current_period_end'   => now()->addMonth(),
        ]);

        return response()->json([
            'message'      => 'Assinatura criada com sucesso.',
            'billing_url'  => $billing['data']['url'] ?? null,
            'subscription' => new SubscriptionResource($subscription),
        ], 201);
    }
}
