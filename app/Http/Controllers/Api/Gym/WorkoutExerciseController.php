<?php

namespace App\Http\Controllers\Api\Gym;

use App\Http\Controllers\Controller;
use App\Http\Resources\WorkoutExerciseResource;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WorkoutExerciseController extends Controller
{
    public function index(Request $request, Workout $workout): AnonymousResourceCollection
    {
        $this->authorize('view', $workout);
        $exercises = $workout->workoutExercises()->with('exercise')->get();

        return WorkoutExerciseResource::collection($exercises);
    }

    public function store(Request $request, Workout $workout): JsonResponse
    {
        $this->authorize('update', $workout);

        $data = $request->validate([
            'exercise_id'  => 'required|exists:exercises,id',
            'sets'         => 'required|integer|min:1|max:20',
            'reps'         => 'required|integer|min:1|max:100',
            'rest_seconds' => 'required|integer|min:0|max:600',
            'order'        => 'nullable|integer|min:0',
        ]);

        $data['order'] ??= $workout->workoutExercises()->max('order') + 1;

        $we = $workout->workoutExercises()->create($data);
        $we->load('exercise');

        return response()->json(['data' => new WorkoutExerciseResource($we)], 201);
    }

    public function update(Request $request, Workout $workout, WorkoutExercise $exercise): JsonResponse
    {
        $this->authorize('update', $workout);

        $data = $request->validate([
            'sets'         => 'sometimes|integer|min:1|max:20',
            'reps'         => 'sometimes|integer|min:1|max:100',
            'rest_seconds' => 'sometimes|integer|min:0|max:600',
            'order'        => 'sometimes|integer|min:0',
        ]);

        $exercise->update($data);
        $exercise->load('exercise');

        return response()->json(['data' => new WorkoutExerciseResource($exercise)]);
    }

    public function destroy(Workout $workout, WorkoutExercise $exercise): JsonResponse
    {
        $this->authorize('update', $workout);
        $exercise->delete();

        return response()->json(['message' => 'Exercício removido do treino.']);
    }

    public function reorder(Request $request, Workout $workout): JsonResponse
    {
        $this->authorize('update', $workout);

        $request->validate([
            'order'   => 'required|array',
            'order.*' => 'integer|exists:workout_exercises,id',
        ]);

        foreach ($request->order as $position => $id) {
            WorkoutExercise::where('id', $id)->where('workout_id', $workout->id)
                ->update(['order' => $position]);
        }

        return response()->json(['message' => 'Ordem atualizada.']);
    }
}
