<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ReportReview extends Model
{
 protected $fillable=['report_id','reviewer_id','level','action','comments','reviewed_at'];
 protected $casts=['reviewed_at'=>'datetime'];
 public function report():BelongsTo{return $this->belongsTo(FourMonthlyReport::class,'report_id');}
 public function reviewer():BelongsTo{return $this->belongsTo(User::class,'reviewer_id');}
}
