<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'workout_id'       => $this->workout_id,
            'workout'          => new WorkoutResource($this->whenLoaded('workout')),
            'date'             => $this->date?->toDateString(),
            'duration_minutes' => $this->duration_minutes,
            'notes'            => $this->notes,
            'sets'             => SessionSetResource::collection($this->whenLoaded('sets')),
            'created_at'       => $this->created_at,
        ];
    }
}
