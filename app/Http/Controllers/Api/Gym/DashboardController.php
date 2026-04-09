<?php

namespace App\Http\Controllers\Api\Gym;

use App\Http\Controllers\Controller;
use App\Models\TrainingSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $lastSession = $user->trainingSessions()
            ->with('workout:id,name')
            ->latest('date')
            ->first();

        $streak = $this->calculateStreak($user->id);

        $weeklySessions = $user->trainingSessions()
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $latestMeasurement = $user->bodyMeasurements()
            ->whereNotNull('weight_kg')
            ->latest('date')
            ->first();

        $measurementThirtyDaysAgo = $user->bodyMeasurements()
            ->whereNotNull('weight_kg')
            ->where('date', '<=', now()->subDays(30))
            ->latest('date')
            ->first();

        $weightChange30d = null;
        if ($latestMeasurement && $measurementThirtyDaysAgo) {
            $weightChange30d = round(
                (float) $latestMeasurement->weight_kg - (float) $measurementThirtyDaysAgo->weight_kg,
                1
            );
        }

        $totalSessions = $user->trainingSessions()->count();

        $suggestedWorkout = $this->suggestWorkout($user->id);

        return response()->json([
            'last_session' => $lastSession ? [
                'date'             => $lastSession->date?->toDateString(),
                'workout_name'     => $lastSession->workout?->name,
                'duration_minutes' => $lastSession->duration_minutes,
            ] : null,
            'streak' => [
                'current' => $streak['current'],
                'best'    => $streak['best'],
            ],
            'weekly_sessions'   => $weeklySessions,
            'latest_weight'     => $latestMeasurement ? (float) $latestMeasurement->weight_kg : null,
            'weight_change_30d' => $weightChange30d,
            'total_sessions'    => $totalSessions,
            'suggested_workout' => $suggestedWorkout,
        ]);
    }

    private function calculateStreak(int $userId): array
    {
        $dates = TrainingSession::where('user_id', $userId)
            ->select(DB::raw('DISTINCT date'))
            ->orderByDesc('date')
            ->pluck('date')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())
            ->values()
            ->toArray();

        if (empty($dates)) {
            return ['current' => 0, 'best' => 0];
        }

        $currentStreak = 0;
        $bestStreak = 0;
        $tempStreak = 1;
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        // streak atual
        $checkDate = in_array($today, $dates) ? $today : (in_array($yesterday, $dates) ? $yesterday : null);

        if ($checkDate) {
            $currentStreak = 1;
            $prev = \Carbon\Carbon::parse($checkDate)->subDay()->toDateString();
            while (in_array($prev, $dates)) {
                $currentStreak++;
                $prev = \Carbon\Carbon::parse($prev)->subDay()->toDateString();
            }
        }

        // best streak
        for ($i = 1; $i < count($dates); $i++) {
            $diff = \Carbon\Carbon::parse($dates[$i - 1])->diffInDays(\Carbon\Carbon::parse($dates[$i]));
            if ($diff === 1) {
                $tempStreak++;
                $bestStreak = max($bestStreak, $tempStreak);
            } else {
                $tempStreak = 1;
            }
        }
        $bestStreak = max($bestStreak, $currentStreak, 1);

        return ['current' => $currentStreak, 'best' => $bestStreak];
    }

    private function suggestWorkout(int $userId): ?array
    {
        $workouts = \App\Models\Workout::where('user_id', $userId)->get();
        if ($workouts->isEmpty()) {
            return null;
        }

        // 0 = Sunday … 6 = Saturday (same as Carbon dayOfWeek)
        $todayDow = (int) now()->dayOfWeek;

        // 1st priority: workout assigned to today
        $todayWorkout = $workouts->first(function ($w) use ($todayDow) {
            return is_array($w->week_days) && in_array($todayDow, $w->week_days);
        });

        if ($todayWorkout) {
            return ['id' => $todayWorkout->id, 'name' => $todayWorkout->name, 'is_scheduled_today' => true];
        }

        // 2nd priority: least recently used
        $lastUsed = \App\Models\TrainingSession::where('user_id', $userId)
            ->select('workout_id', DB::raw('MAX(date) as last_date'))
            ->groupBy('workout_id')
            ->pluck('last_date', 'workout_id');

        $suggested = $workouts->sortBy(function ($workout) use ($lastUsed) {
            return $lastUsed[$workout->id] ?? '1900-01-01';
        })->first();

        return $suggested ? ['id' => $suggested->id, 'name' => $suggested->name, 'is_scheduled_today' => false] : null;
    }
}
