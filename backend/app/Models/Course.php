<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    protected $fillable = ['code', 'name', 'description', 'duration_days', 'status'];
    public function cadets(): BelongsToMany { return $this->belongsToMany(Cadet::class)->withPivot(['status','completed_at','result'])->withTimestamps(); }
}
