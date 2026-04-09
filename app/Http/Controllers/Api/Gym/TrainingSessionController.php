<?php

namespace App\Http\Controllers\Api\Gym;

use App\Http\Controllers\Controller;
use App\Http\Resources\TrainingSessionResource;
use App\Models\TrainingSession;
use App\Models\Workout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TrainingSessionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $sessions = $request->user()->trainingSessions()
            ->with(['workout.workoutExercises.exercise', 'sets'])
            ->latest('date')
            ->paginate(20);

        return TrainingSessionResource::collection($sessions);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'workout_id' => 'required|exists:workouts,id',
            'date'       => 'required|date',
        ]);

        $workout = Workout::findOrFail($data['workout_id']);
        $this->authorize('view', $workout);

        $session = $request->user()->trainingSessions()->create($data);
        $session->load('workout');

        return response()->json(['data' => new TrainingSessionResource($session)], 201);
    }

    public function show(Request $request, TrainingSession $session): JsonResponse
    {
        $this->authorize('view', $session);
        $session->load(['workout.workoutExercises.exercise', 'sets']);

        return response()->json(['data' => new TrainingSessionResource($session)]);
    }

    public function finish(Request $request, TrainingSession $session): JsonResponse
    {
        $this->authorize('update', $session);

        $data = $request->validate([
            'duration_minutes' => 'required|integer|min:1',
            'notes'            => 'nullable|string',
        ]);

        $session->update($data);

        return response()->json(['data' => new TrainingSessionResource($session)]);
    }

    public function destroy(TrainingSession $session): JsonResponse
    {
        $this->authorize('delete', $session);
        $session->delete();

        return response()->json(['message' => 'Sessão removida.']);
    }

    public function previous(Request $request, TrainingSession $session): JsonResponse
    {
        $this->authorize('view', $session);

        $previous = $request->user()->trainingSessions()
            ->where('workout_id', $session->workout_id)
            ->where('id', '<', $session->id)
            ->with(['sets'])
            ->latest('date')
            ->first();

        if (! $previous) {
            return response()->json(['data' => null]);
        }

        return response()->json(['data' => new TrainingSessionResource($previous)]);
    }
}
