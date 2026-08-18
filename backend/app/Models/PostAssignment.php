<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostAssignment extends Model
{
    protected $fillable = ['cadet_id', 'post_id', 'start_date', 'end_date', 'status', 'reference'];
    protected function casts(): array { return ['start_date' => 'date', 'end_date' => 'date']; }
    public function cadet(): BelongsTo { return $this->belongsTo(Cadet::class); }
    public function post(): BelongsTo { return $this->belongsTo(Post::class); }
}
