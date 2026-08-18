<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cadet;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PromotionController extends Controller
{
    public function store(Request $request, Cadet $cadet): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        $data = $request->validate(['new_rank_id' => ['required', 'exists:ranks,id'], 'promotion_date' => ['required', 'date'], 'reason' => ['nullable', 'string', 'max:2000']]);
        $currentRank = $cadet->rank()->firstOrFail();
        $newRank = $cadet->rank()->getRelated()->newQuery()->findOrFail($data['new_rank_id']);
        if ((int) $newRank->order <= (int) $currentRank->order) {
            throw ValidationException::withMessages(['new_rank_id' => 'The new rank must be higher than the cadet\'s current rank.']);
        }
        $promotion = DB::transaction(function () use ($cadet, $currentRank, $newRank, $data): Promotion {
            $promotion = Promotion::create([
                'cadet_id' => $cadet->id,
                'from_rank_id' => $currentRank->id,
                'to_rank_id' => $newRank->id,
                'promoted_at' => $data['promotion_date'],
                'reason' => $data['reason'] ?? null,
                'reference' => 'NACO-PROM-' . now()->format('YmdHis') . '-' . bin2hex(random_bytes(3)),
                'status' => 'approved',
            ]);
            $cadet->update(['rank_id' => $newRank->id]);
            return $promotion;
        });
        return response()->json($promotion->load(['fromRank', 'toRank']), 201);
    }
}
