<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Course extends Model
{
 protected $fillable=['code','name','description','duration_days','status','fee','is_instructor_course'];
 protected function casts():array{return ['fee'=>'decimal:2','is_instructor_course'=>'boolean'];}
 public function cadets():BelongsToMany{return $this->belongsToMany(Cadet::class)->withPivot(['status','completed_at','result','payment_reference','paid_at'])->withTimestamps();}
 public function warrants(){return $this->hasMany(Warrant::class);}
}
