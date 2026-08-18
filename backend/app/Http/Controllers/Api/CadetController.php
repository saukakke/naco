<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cadet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CadetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        return response()->json(Cadet::with(['unit', 'ward', 'rank'])->paginate(20));
    }

    public function show(Request $request, Cadet $cadet): JsonResponse
    {
        abort_unless($request->user()->isAdmin() || (int) $request->user()->cadet_id === (int) $cadet->id, 403);
        return response()->json($cadet->load(['unit', 'ward', 'rank', 'courses', 'warrants', 'promotions', 'demotions']));
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        $data = $request->validate([
            'service_number' => ['required', 'string', 'max:50', 'unique:cadets,service_number'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'gender' => ['nullable', 'string', 'max:30'],
            'date_of_birth' => ['nullable', 'date'],
            'unit_id' => ['required', 'exists:units,id'],
            'ward_id' => ['nullable', 'exists:wards,id'],
            'rank_id' => ['required', 'exists:ranks,id'],
            'status' => ['nullable', 'in:active,inactive,suspended,retired'],
            'id_card_expires_at' => ['nullable', 'date'],
        ]);
        return response()->json(Cadet::create($data), 201);
    }

    public function update(Request $request, Cadet $cadet): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        $data = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:100'],
            'middle_name' => ['sometimes', 'nullable', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'gender' => ['sometimes', 'nullable', 'string', 'max:30'],
            'unit_id' => ['sometimes', 'exists:units,id'],
            'ward_id' => ['sometimes', 'nullable', 'exists:wards,id'],
            'rank_id' => ['sometimes', 'exists:ranks,id'],
            'status' => ['sometimes', 'in:active,inactive,suspended,retired'],
            'id_card_expires_at' => ['sometimes', 'nullable', 'date'],
        ]);
        $cadet->update($data);
        return response()->json($cadet->fresh(['unit', 'ward', 'rank']));
    }
}
