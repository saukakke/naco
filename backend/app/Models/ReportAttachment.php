<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ReportAttachment extends Model
{
 protected $fillable=['report_id','uploaded_by','file_path','original_name','mime_type','size'];
 public function report():BelongsTo{return $this->belongsTo(FourMonthlyReport::class,'report_id');}
 public function uploader():BelongsTo{return $this->belongsTo(User::class,'uploaded_by');}
}
