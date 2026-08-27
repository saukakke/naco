<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Demotion extends Model
{
    protected $fillable = ['cadet_id', 'from_rank_id', 'to_rank_id', 'demoted_at', 'reason', 'reference', 'document_path', 'status'];
    protected function casts(): array { return ['demoted_at' => 'date']; }
    public function cadet(): BelongsTo { return $this->belongsTo(Cadet::class, 'cadet_id', 'service_number'); }
    public function fromRank(): BelongsTo { return $this->belongsTo(Rank::class, 'from_rank_id'); }
    public function toRank(): BelongsTo { return $this->belongsTo(Rank::class, 'to_rank_id'); }
}
