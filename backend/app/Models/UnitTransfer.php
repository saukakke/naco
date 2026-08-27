<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitTransfer extends Model
{
    protected $fillable = ['cadet_id','from_unit_id','to_unit_id','reason','status','applied_at','released_at','accepted_at','payment_verified_at','completed_at','released_by','accepted_by','payment_reference'];
    protected $casts = ['applied_at'=>'datetime','released_at'=>'datetime','accepted_at'=>'datetime','payment_verified_at'=>'datetime','completed_at'=>'datetime'];

    public function cadet(): BelongsTo { return $this->belongsTo(Cadet::class, 'cadet_id', 'service_number'); }
    public function fromUnit(): BelongsTo { return $this->belongsTo(Unit::class, 'from_unit_id'); }
    public function toUnit(): BelongsTo { return $this->belongsTo(Unit::class, 'to_unit_id'); }
    public function releasedBy(): BelongsTo { return $this->belongsTo(User::class, 'released_by'); }
    public function acceptedBy(): BelongsTo { return $this->belongsTo(User::class, 'accepted_by'); }
}
