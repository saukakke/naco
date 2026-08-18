<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cadet;
use App\Models\Demotion;
use App\Models\Rank;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DemotionService
{
    public function demote(Cadet $cadet, Rank $newRank, array $attributes = []): Demotion
    {
        if ($newRank->order >= $cadet->rank->order) {
            throw ValidationException::withMessages(['rank_id' => 'The new rank must be lower than the cadet current rank.']);
        }

        return DB::transaction(function () use ($cadet, $newRank, $attributes): Demotion {
            $demotion = Demotion::create([
                'cadet_id' => $cadet->id,
                'from_rank_id' => $cadet->rank_id,
                'to_rank_id' => $newRank->id,
                'demoted_at' => $attributes['demoted_at'] ?? now()->toDateString(),
                'reason' => $attributes['reason'] ?? null,
                'reference' => $attributes['reference'] ?? 'DEM-'.now()->format('YmdHis').'-'.$cadet->id,
                'status' => 'approved',
            ]);
            $cadet->update(['rank_id' => $newRank->id]);
            return $demotion;
        });
    }
}
