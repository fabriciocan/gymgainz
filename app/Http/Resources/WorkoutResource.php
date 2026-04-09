<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkoutResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'name'                    => $this->name,
            'description'             => $this->description,
            'week_days'               => $this->week_days ?? [],
            'workout_exercises_count' => $this->workout_exercises_count ?? $this->workoutExercises?->count(),
            'exercises'               => WorkoutExerciseResource::collection($this->whenLoaded('workoutExercises')),
            'created_at'              => $this->created_at,
            'updated_at'              => $this->updated_at,
        ];
    }
}
