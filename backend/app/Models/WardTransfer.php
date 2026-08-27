<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WardTransfer extends Model
{
    protected $fillable=['cadet_id','from_ward_id','to_ward_id','reference','status','reason','source_hcs_released_at','source_hcs_id','source_lga_acknowledged_at','source_lga_acknowledged_by','source_state_acknowledged_at','source_state_acknowledged_by','destination_hcs_accepted_at','destination_hcs_id','destination_lga_acknowledged_at','destination_lga_acknowledged_by','destination_state_acknowledged_at','destination_state_acknowledged_by','national_approved_at','national_approved_by','completed_at'];
    protected $casts=['source_hcs_released_at'=>'datetime','source_lga_acknowledged_at'=>'datetime','source_state_acknowledged_at'=>'datetime','destination_hcs_accepted_at'=>'datetime','destination_lga_acknowledged_at'=>'datetime','destination_state_acknowledged_at'=>'datetime','national_approved_at'=>'datetime','completed_at'=>'datetime'];

    public function cadet():BelongsTo{return $this->belongsTo(Cadet::class,'cadet_id','service_number');}
    public function fromWard():BelongsTo{return $this->belongsTo(Ward::class,'from_ward_id');}
    public function toWard():BelongsTo{return $this->belongsTo(Ward::class,'to_ward_id');}
    public function sourceHcs():BelongsTo{return $this->belongsTo(User::class,'source_hcs_id');}
    public function sourceLgaAcknowledgedBy():BelongsTo{return $this->belongsTo(User::class,'source_lga_acknowledged_by');}
    public function sourceStateAcknowledgedBy():BelongsTo{return $this->belongsTo(User::class,'source_state_acknowledged_by');}
    public function destinationHcs():BelongsTo{return $this->belongsTo(User::class,'destination_hcs_id');}
    public function destinationLgaAcknowledgedBy():BelongsTo{return $this->belongsTo(User::class,'destination_lga_acknowledged_by');}
    public function destinationStateAcknowledgedBy():BelongsTo{return $this->belongsTo(User::class,'destination_state_acknowledged_by');}
    public function nationalApprovedBy():BelongsTo{return $this->belongsTo(User::class,'national_approved_by');}
}
