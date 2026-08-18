<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cadet extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_number', 'first_name', 'middle_name', 'last_name', 'phone',
        'email', 'gender', 'date_of_birth', 'unit_id', 'rank_id', 'status',
    ];

    protected function casts(): array
    {
        return ['date_of_birth' => 'date'];
    }

    public function unit(): BelongsTo { return $this->belongsTo(Unit::class); }
    public function rank(): BelongsTo { return $this->belongsTo(Rank::class); }
    public function courses(): BelongsToMany { return $this->belongsToMany(Course::class)->withPivot(['status','completed_at','result'])->withTimestamps(); }
    public function warrants(): HasMany { return $this->hasMany(Warrant::class); }
    public function promotions(): HasMany { return $this->hasMany(Promotion::class); }
    public function demotions(): HasMany { return $this->hasMany(Demotion::class); }
    public function postAssignments(): HasMany { return $this->hasMany(PostAssignment::class); }
}
