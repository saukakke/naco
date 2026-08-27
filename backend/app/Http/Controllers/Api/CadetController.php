<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cadet;
use App\Services\AuthorizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CadetController extends Controller
{
    public function index(Request $request, AuthorizationService $authorization): JsonResponse
    {
        $cadets = $authorization->cadetQuery($request->user())
            ->with(['unit', 'ward.lga.state', 'rank.category'])
            ->latest('created_at')
            ->paginate(20);

        return response()->json($cadets);
    }

    public function show(Request $request, Cadet $cadet, AuthorizationService $authorization): JsonResponse
    {
        abort_unless($authorization->canAccessCadet($request->user(), $cadet), 403);

        return response()->json($cadet->load([
            'unit', 'ward.lga.state', 'rank.category', 'courses', 'warrants.course', 'promotions.fromRank', 'promotions.toRank', 'demotions.fromRank', 'demotions.toRank',
        ]));
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasGlobalAccess(), 403);

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

    public function update(Request $request, Cadet $cadet, AuthorizationService $authorization): JsonResponse
    {
        abort_unless($authorization->canManageCadet($request->user(), $cadet), 403);

        $rules = [
            'first_name' => ['sometimes', 'string', 'max:100'],
            'middle_name' => ['sometimes', 'nullable', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'gender' => ['sometimes', 'nullable', 'string', 'max:30'],
            'date_of_birth' => ['sometimes', 'nullable', 'date'],
            'id_card_expires_at' => ['sometimes', 'nullable', 'date'],
        ];

        if ($request->user()->hasGlobalAccess()) {
            $rules += [
                'unit_id' => ['sometimes', 'exists:units,id'],
                'ward_id' => ['sometimes', 'nullable', 'exists:wards,id'],
                'rank_id' => ['sometimes', 'exists:ranks,id'],
                'status' => ['sometimes', 'in:active,inactive,suspended,retired'],
            ];
        }

        $cadet->update($request->validate($rules));

        return response()->json($cadet->fresh(['unit', 'ward.lga.state', 'rank.category']));
    }
}
