<?php

namespace App\Http\Controllers\Api\Gym;

use App\Http\Controllers\Controller;
use App\Http\Resources\BodyMeasurementResource;
use App\Models\BodyMeasurement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BodyMeasurementController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $measurements = $request->user()->bodyMeasurements()
            ->orderByDesc('date')
            ->get();

        return BodyMeasurementResource::collection($measurements);
    }

    public function latest(Request $request): JsonResponse
    {
        $measurement = $request->user()->bodyMeasurements()
            ->latest('date')
            ->first();

        return response()->json(
            $measurement ? new BodyMeasurementResource($measurement) : null
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'date'         => 'required|date',
            'weight_kg'      => 'nullable|numeric|min:0',
            'body_fat_pct'   => 'nullable|numeric|min:0|max:100',
            'bicep_left_cm'  => 'nullable|numeric|min:0',
            'bicep_right_cm' => 'nullable|numeric|min:0',
            'chest_cm'       => 'nullable|numeric|min:0',
            'waist_cm'       => 'nullable|numeric|min:0',
            'abdomen_cm'     => 'nullable|numeric|min:0',
            'hip_cm'         => 'nullable|numeric|min:0',
            'thigh_left_cm'  => 'nullable|numeric|min:0',
            'thigh_right_cm' => 'nullable|numeric|min:0',
            'calf_left_cm'   => 'nullable|numeric|min:0',
            'calf_right_cm'  => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
        ]);

        $measurement = $request->user()->bodyMeasurements()->create($data);

        return response()->json(['data' => new BodyMeasurementResource($measurement)], 201);
    }

    public function update(Request $request, BodyMeasurement $measurement): JsonResponse
    {
        $this->authorize('update', $measurement);

        $data = $request->validate([
            'date'         => 'sometimes|date',
            'weight_kg'      => 'nullable|numeric|min:0',
            'body_fat_pct'   => 'nullable|numeric|min:0|max:100',
            'bicep_left_cm'  => 'nullable|numeric|min:0',
            'bicep_right_cm' => 'nullable|numeric|min:0',
            'chest_cm'       => 'nullable|numeric|min:0',
            'waist_cm'       => 'nullable|numeric|min:0',
            'abdomen_cm'     => 'nullable|numeric|min:0',
            'hip_cm'         => 'nullable|numeric|min:0',
            'thigh_left_cm'  => 'nullable|numeric|min:0',
            'thigh_right_cm' => 'nullable|numeric|min:0',
            'calf_left_cm'   => 'nullable|numeric|min:0',
            'calf_right_cm'  => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
        ]);

        $measurement->update($data);

        return response()->json(['data' => new BodyMeasurementResource($measurement)]);
    }

    public function destroy(BodyMeasurement $measurement): JsonResponse
    {
        $this->authorize('delete', $measurement);
        $measurement->delete();

        return response()->json(['message' => 'Medida removida.']);
    }
}
