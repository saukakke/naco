<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rank extends Model
{
    protected $fillable = ['rank_category_id', 'name', 'slug', 'order'];
    public function category(): BelongsTo { return $this->belongsTo(RankCategory::class, 'rank_category_id'); }
    public function cadets(): HasMany { return $this->hasMany(Cadet::class); }
}
