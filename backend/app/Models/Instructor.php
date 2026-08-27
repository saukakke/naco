<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Instructor extends Model
{
    protected $fillable=['cadet_id','status'];
    public function cadet():BelongsTo{return $this->belongsTo(Cadet::class,'cadet_id','service_number');}
    public function getIsActiveAttribute():bool{return $this->cadet?->hasValidWarrant() ?? false;}
}
