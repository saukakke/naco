<?php
declare(strict_types=1);
namespace App\Services;
use App\Models\Cadet;
use App\Models\Course;
use App\Models\Warrant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class InstructorQualificationService
{
 public function hasValidWarrant(Cadet $cadet):bool{return $cadet->warrants()->where('status','active')->whereDate('issued_at','<=',today())->whereDate('expires_at','>',today())->exists();}
 public function enroll(Cadet $cadet, Course $course):void{if(!$course->is_instructor_course)throw ValidationException::withMessages(['course'=>'This course does not qualify cadets for an instructor warrant.']);if($this->hasValidWarrant($cadet))throw ValidationException::withMessages(['warrant'=>'Cadet already has a valid instructor warrant.']);if($cadet->courses()->where('course_id',$course->id)->wherePivotIn('status',['enrolled','paid','passed'])->exists())throw ValidationException::withMessages(['course'=>'Cadet already has an active enrollment for this course.']);$cadet->courses()->attach($course->id,['status'=>'enrolled']);}
 public function verifyPayment(Cadet $cadet, Course $course, string $reference):void{$pivot=$cadet->courses()->where('course_id',$course->id)->first();if(!$pivot||$pivot->pivot->status!=='enrolled')throw ValidationException::withMessages(['course'=>'No payable course enrollment was found.']);$cadet->courses()->updateExistingPivot($course->id,['status'=>'paid','payment_reference'=>$reference]);}
 public function recordResult(Cadet $cadet, Course $course, bool $passed):void{$pivot=$cadet->courses()->where('course_id',$course->id)->first();if(!$pivot||$pivot->pivot->status!=='paid')throw ValidationException::withMessages(['course'=>'Payment must be verified before recording the result.']);$cadet->courses()->updateExistingPivot($course->id,['status'=>$passed?'passed':'failed','completed_at'=>now(),'result'=>$passed?'pass':'fail']);}
 public function issueWarrant(Cadet $cadet, Course $course, int $validityMonths=24):Warrant{if(!$this->hasValidPassedCourse($cadet,$course))throw ValidationException::withMessages(['warrant'=>'The cadet must pass and pay for the qualifying course before a warrant is issued.']);return DB::transaction(function()use($cadet,$course,$validityMonths){$w=Warrant::create(['cadet_id'=>$cadet->id,'course_id'=>$course->id,'warrant_number'=>'NACO-WR-'.now()->format('YmdHis').'-'.$cadet->id,'type'=>'instructor','issued_at'=>today(),'expires_at'=>Carbon::today()->addMonths($validityMonths),'status'=>'active']);$cadet->instructor()->updateOrCreate([],['status'=>'active']);return $w;});}
 private function hasValidPassedCourse(Cadet $cadet,Course $course):bool{$p=$cadet->courses()->where('course_id',$course->id)->first();return (bool)$p&&$p->pivot->status==='passed'&&$p->pivot->payment_reference;}
 public function expireWarrants():int{return Warrant::where('status','active')->whereDate('expires_at','<=',today())->update(['status'=>'expired']);}
}
