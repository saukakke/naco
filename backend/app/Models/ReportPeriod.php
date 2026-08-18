<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class ReportPeriod extends Model
{
 protected $fillable=['year','period','starts_on','ends_on','due_on','status'];
 protected $casts=['starts_on'=>'date','ends_on'=>'date','due_on'=>'date'];
 public function reports():HasMany{return $this->hasMany(FourMonthlyReport::class,'report_period_id');}
}
