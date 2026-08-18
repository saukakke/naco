<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RankCategory extends Model
{
    protected $fillable = ['name', 'slug', 'order'];
    public function ranks(): HasMany { return $this->hasMany(Rank::class); }
}
