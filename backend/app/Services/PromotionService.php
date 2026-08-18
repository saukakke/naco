<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cadet;
use App\Models\Promotion;
use App\Models\Rank;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PromotionService
{
    public function promote(Cadet $cadet, Rank $newRank, array $attributes = []): Promotion
    {
        if ($newRank->order <= $cadet->rank->order) {
            throw ValidationException::withMessages(['rank_id' => 'The new rank must be higher than the cadet current rank.']);
        }

        return DB::transaction(function () use ($cadet, $newRank, $attributes): Promotion {
            $promotion = Promotion::create([
                'cadet_id' => $cadet->id,
                'from_rank_id' => $cadet->rank_id,
                'to_rank_id' => $newRank->id,
                'promoted_at' => $attributes['promoted_at'] ?? now()->toDateString(),
                'reason' => $attributes['reason'] ?? null,
                'reference' => $attributes['reference'] ?? 'PROM-'.now()->format('YmdHis').'-'.$cadet->id,
                'status' => 'approved',
            ]);
            $cadet->update(['rank_id' => $newRank->id]);
            return $promotion;
        });
    }
}
