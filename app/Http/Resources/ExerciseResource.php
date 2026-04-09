<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'muscle_group' => $this->muscle_group,
            'is_global'    => $this->user_id === null,
            'user_id'      => $this->user_id,
            'created_at'   => $this->created_at,
        ];
    }
}
