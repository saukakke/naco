<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cadet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CadetController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Cadet::with(['unit', 'rank', 'post'])->paginate(20));
    }

    public function show(Cadet $cadet): JsonResponse
    {
        return response()->json($cadet->load(['unit', 'rank', 'post', 'courses', 'warrants', 'promotions', 'demotions']));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'service_number' => ['required', 'string', 'max:50', 'unique:cadets,service_number'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'unit_id' => ['required', 'exists:units,id'],
            'rank_id' => ['required', 'exists:ranks,id'],
            'post_id' => ['nullable', 'exists:posts,id'],
        ]);

        return response()->json(Cadet::create($data), 201);
    }

    public function update(Request $request, Cadet $cadet): JsonResponse
    {
        $data = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'unit_id' => ['sometimes', 'exists:units,id'],
            'post_id' => ['nullable', 'exists:posts,id'],
        ]);

        $cadet->update($data);
        return response()->json($cadet->fresh(['unit', 'rank', 'post']));
    }
}
