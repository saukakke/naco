<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonnelDocument extends Model
{
    protected $fillable=['cadet_id','document_type','title','file_path','document_number','issued_at','expires_at','metadata'];
    protected $casts=['issued_at'=>'date','expires_at'=>'date','metadata'=>'array'];
    public function cadet():BelongsTo{return $this->belongsTo(Cadet::class,'cadet_id','service_number');}
}
