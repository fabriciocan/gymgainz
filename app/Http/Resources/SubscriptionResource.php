<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'status'               => $this->status,
            'plan'                 => $this->plan,
            'amount_cents'         => $this->amount_cents,
            'current_period_start' => $this->current_period_start?->toDateTimeString(),
            'current_period_end'   => $this->current_period_end?->toDateTimeString(),
            'is_active'            => $this->isActive(),
        ];
    }
}
