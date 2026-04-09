<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SessionSetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'session_id'          => $this->session_id,
            'workout_exercise_id' => $this->workout_exercise_id,
            'set_number'          => $this->set_number,
            'reps_done'           => $this->reps_done,
            'weight_kg'           => (float) $this->weight_kg,
            'created_at'          => $this->created_at,
        ];
    }
}
