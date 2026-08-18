<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warrant extends Model
{
    protected $fillable = ['cadet_id', 'course_id', 'warrant_number', 'type', 'issued_at', 'expires_at', 'status', 'document_path'];
    protected function casts(): array { return ['issued_at' => 'date', 'expires_at' => 'date']; }
    public function cadet(): BelongsTo { return $this->belongsTo(Cadet::class); }
    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
}
