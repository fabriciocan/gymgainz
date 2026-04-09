<?php

namespace App\Http\Controllers\Api\Gym;

use App\Http\Controllers\Controller;
use App\Http\Resources\SessionSetResource;
use App\Models\SessionSet;
use App\Models\TrainingSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionSetController extends Controller
{
    public function store(Request $request, TrainingSession $session): JsonResponse
    {
        $this->authorize('update', $session);

        $data = $request->validate([
            'workout_exercise_id' => 'required|exists:workout_exercises,id',
            'set_number'          => 'required|integer|min:1',
            'reps_done'           => 'required|integer|min:0',
            'weight_kg'           => 'required|numeric|min:0',
        ]);

        $set = $session->sets()->create($data);

        return response()->json(['data' => new SessionSetResource($set)], 201);
    }

    public function update(Request $request, TrainingSession $session, SessionSet $set): JsonResponse
    {
        $this->authorize('update', $session);

        $data = $request->validate([
            'reps_done' => 'sometimes|integer|min:0',
            'weight_kg' => 'sometimes|numeric|min:0',
        ]);

        $set->update($data);

        return response()->json(['data' => new SessionSetResource($set)]);
    }

    public function destroy(TrainingSession $session, SessionSet $set): JsonResponse
    {
        $this->authorize('update', $session);
        $set->delete();

        return response()->json(['message' => 'Set removido.']);
    }
}
