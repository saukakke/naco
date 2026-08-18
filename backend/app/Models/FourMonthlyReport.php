<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class FourMonthlyReport extends Model
{
 protected $fillable=['ward_id','report_period_id','submitted_by','status','summary','activities','membership','training','self_reliance','finance','submitted_at','finalized_at'];
 protected $casts=['activities'=>'array','membership'=>'array','training'=>'array','self_reliance'=>'array','finance'=>'array','submitted_at'=>'datetime','finalized_at'=>'datetime'];
 public function ward():BelongsTo{return $this->belongsTo(Ward::class);}
 public function period():BelongsTo{return $this->belongsTo(ReportPeriod::class,'report_period_id');}
 public function submitter():BelongsTo{return $this->belongsTo(User::class,'submitted_by');}
 public function reviews():HasMany{return $this->hasMany(ReportReview::class,'report_id');}
 public function attachments():HasMany{return $this->hasMany(ReportAttachment::class,'report_id');}
}
