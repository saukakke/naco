<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdCardRenewalApplication extends Model
{
    protected $fillable = ['cadet_id','reference','current_card_expires_at','status','reason','payment_reference','payment_verified_at','approved_at','issued_at','approved_by'];
    protected $casts = ['current_card_expires_at'=>'date','payment_verified_at'=>'datetime','approved_at'=>'datetime','issued_at'=>'datetime'];
    public function cadet(): BelongsTo { return $this->belongsTo(Cadet::class, 'cadet_id', 'service_number'); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
}
