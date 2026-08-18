<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cadet;
use App\Models\Demotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DemotionController extends Controller
{
    public function store(Request $request, Cadet $cadet): JsonResponse
    {
        $data = $request->validate([
            'new_rank_id' => ['required', 'exists:ranks,id'],
            'demotion_date' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:2000'],
        ]);

        $currentRank = $cadet->rank()->firstOrFail();
        $newRank = $cadet->rank()->getRelated()->newQuery()->findOrFail($data['new_rank_id']);

        if ((int) $newRank->order >= (int) $currentRank->order) {
            throw ValidationException::withMessages([
                'new_rank_id' => 'The new rank must be lower than the cadet\'s current rank.',
            ]);
        }

        $demotion = DB::transaction(function () use ($cadet, $currentRank, $newRank, $data): Demotion {
            $demotion = Demotion::create([
                'cadet_id' => $cadet->id,
                'previous_rank_id' => $currentRank->id,
                'new_rank_id' => $newRank->id,
                'demotion_date' => $data['demotion_date'],
                'reason' => $data['reason'],
            ]);

            $cadet->update(['rank_id' => $newRank->id]);
            return $demotion;
        });

        return response()->json($demotion->load(['previousRank', 'newRank']), 201);
    }
}
