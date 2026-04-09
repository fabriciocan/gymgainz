<?php

namespace App\Http\Controllers\Api\Gym;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExerciseResource;
use App\Models\Exercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExerciseController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $exercises = Exercise::where(function ($q) use ($request) {
            $q->whereNull('user_id')->orWhere('user_id', $request->user()->id);
        })
        ->when($request->search, fn($q, $s) => $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($s) . '%']))
        ->when($request->muscle_group, fn($q, $m) => $q->where('muscle_group', $m))
        ->orderByRaw('user_id IS NOT NULL, name')
        ->get();

        return ExerciseResource::collection($exercises);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'muscle_group' => 'required|in:chest,back,shoulders,biceps,triceps,forearms,core,glutes,quadriceps,hamstrings,calves,full_body',
        ]);

        $exercise = Exercise::create([
            'user_id'      => $request->user()->id,
            'name'         => $data['name'],
            'muscle_group' => $data['muscle_group'],
        ]);

        return response()->json(['data' => new ExerciseResource($exercise)], 201);
    }

    public function update(Request $request, Exercise $exercise): JsonResponse
    {
        $this->authorize('update', $exercise);

        $data = $request->validate([
            'name'         => 'sometimes|string|max:255',
            'muscle_group' => 'sometimes|in:chest,back,shoulders,biceps,triceps,forearms,core,glutes,quadriceps,hamstrings,calves,full_body',
        ]);

        $exercise->update($data);

        return response()->json(['data' => new ExerciseResource($exercise)]);
    }

    public function destroy(Exercise $exercise): JsonResponse
    {
        $this->authorize('delete', $exercise);
        $exercise->delete();

        return response()->json(['message' => 'Exercício removido.']);
    }
}
