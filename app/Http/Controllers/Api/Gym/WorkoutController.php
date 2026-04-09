<?php

namespace App\Http\Controllers\Api\Gym;

use App\Http\Controllers\Controller;
use App\Http\Resources\WorkoutResource;
use App\Models\Workout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WorkoutController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $workouts = $request->user()->workouts()->withCount('workoutExercises')->latest()->get();
        return WorkoutResource::collection($workouts);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'week_days'   => 'nullable|array',
            'week_days.*' => 'integer|between:0,6',
        ]);

        $workout = $request->user()->workouts()->create($data);

        return response()->json(['data' => new WorkoutResource($workout)], 201);
    }

    public function show(Request $request, Workout $workout): JsonResponse
    {
        $this->authorize('view', $workout);
        $workout->load(['workoutExercises.exercise']);

        return response()->json(['data' => new WorkoutResource($workout)]);
    }

    public function update(Request $request, Workout $workout): JsonResponse
    {
        $this->authorize('update', $workout);

        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'week_days'   => 'nullable|array',
            'week_days.*' => 'integer|between:0,6',
        ]);

        $workout->update($data);

        return response()->json(['data' => new WorkoutResource($workout)]);
    }

    public function destroy(Workout $workout): JsonResponse
    {
        $this->authorize('delete', $workout);
        $workout->delete();

        return response()->json(['message' => 'Treino removido.']);
    }
}
