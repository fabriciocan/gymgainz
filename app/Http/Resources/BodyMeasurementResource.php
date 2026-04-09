<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BodyMeasurementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'date'           => $this->date?->toDateString(),
            'weight_kg'      => $this->weight_kg !== null ? (float) $this->weight_kg : null,
            'body_fat_pct'   => $this->body_fat_pct !== null ? (float) $this->body_fat_pct : null,
            'bicep_left_cm'  => $this->bicep_left_cm !== null ? (float) $this->bicep_left_cm : null,
            'bicep_right_cm' => $this->bicep_right_cm !== null ? (float) $this->bicep_right_cm : null,
            'chest_cm'       => $this->chest_cm !== null ? (float) $this->chest_cm : null,
            'waist_cm'       => $this->waist_cm !== null ? (float) $this->waist_cm : null,
            'abdomen_cm'     => $this->abdomen_cm !== null ? (float) $this->abdomen_cm : null,
            'hip_cm'         => $this->hip_cm !== null ? (float) $this->hip_cm : null,
            'thigh_left_cm'  => $this->thigh_left_cm !== null ? (float) $this->thigh_left_cm : null,
            'thigh_right_cm' => $this->thigh_right_cm !== null ? (float) $this->thigh_right_cm : null,
            'calf_left_cm'   => $this->calf_left_cm !== null ? (float) $this->calf_left_cm : null,
            'calf_right_cm'  => $this->calf_right_cm !== null ? (float) $this->calf_right_cm : null,
            'notes'          => $this->notes,
            'created_at'     => $this->created_at,
        ];
    }
}
