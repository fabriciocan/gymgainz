<?php

namespace App\Http\Controllers\Api\Gym;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\SessionSet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    private function periodStart(string $period): ?\Carbon\Carbon
    {
        return match ($period) {
            '30d'   => now()->subDays(30),
            '90d'   => now()->subDays(90),
            '6m'    => now()->subMonths(6),
            '1y'    => now()->subYear(),
            default => null,
        };
    }

    public function exercise(Request $request, Exercise $exercise): JsonResponse
    {
        $periodStart = $this->periodStart($request->input('period', 'all'));

        $data = SessionSet::query()
            ->join('training_sessions', 'session_sets.session_id', '=', 'training_sessions.id')
            ->join('workout_exercises', 'session_sets.workout_exercise_id', '=', 'workout_exercises.id')
            ->where('training_sessions.user_id', $request->user()->id)
            ->where('workout_exercises.exercise_id', $exercise->id)
            ->when($periodStart, fn($q) => $q->where('training_sessions.date', '>=', $periodStart))
            ->select(
                'training_sessions.date',
                DB::raw('MAX(session_sets.weight_kg) as max_weight'),
                DB::raw('ROUND(AVG(session_sets.reps_done), 1) as avg_reps')
            )
            ->groupBy('training_sessions.date')
            ->orderBy('training_sessions.date')
            ->get()
            ->map(fn($row) => [
                'date'       => $row->date,
                'max_weight' => (float) $row->max_weight,
                'avg_reps'   => (float) $row->avg_reps,
            ]);

        return response()->json([
            'exercise' => ['id' => $exercise->id, 'name' => $exercise->name],
            'data'     => $data,
        ]);
    }

    public function volume(Request $request, Exercise $exercise): JsonResponse
    {
        $periodStart = $this->periodStart($request->input('period', 'all'));

        $data = SessionSet::query()
            ->join('training_sessions', 'session_sets.session_id', '=', 'training_sessions.id')
            ->join('workout_exercises', 'session_sets.workout_exercise_id', '=', 'workout_exercises.id')
            ->where('training_sessions.user_id', $request->user()->id)
            ->where('workout_exercises.exercise_id', $exercise->id)
            ->when($periodStart, fn($q) => $q->where('training_sessions.date', '>=', $periodStart))
            ->select(
                'training_sessions.date',
                DB::raw('SUM(session_sets.reps_done * session_sets.weight_kg) as volume')
            )
            ->groupBy('training_sessions.date')
            ->orderBy('training_sessions.date')
            ->get()
            ->map(fn($row) => [
                'date'   => $row->date,
                'volume' => (float) $row->volume,
            ]);

        return response()->json([
            'exercise' => ['id' => $exercise->id, 'name' => $exercise->name],
            'data'     => $data,
        ]);
    }
}
