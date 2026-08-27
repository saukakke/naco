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
    public function hasValidWarrant(Cadet $cadet): bool
    {
        return $cadet->warrants()->where('status','active')->whereDate('issued_at','<=',today())->whereDate('expires_at','>',today())->exists();
    }

    public function hasValidWarrantForCourse(Cadet $cadet, Course $course): bool
    {
        return $cadet->warrants()->where('course_id',$course->id)->where('status','active')->whereDate('issued_at','<=',today())->whereDate('expires_at','>',today())->exists();
    }

    public function enroll(Cadet $cadet, Course $course): void
    {
        $this->assertInstructorCourse($course);
        if ($this->hasValidWarrantForCourse($cadet,$course)) throw ValidationException::withMessages(['warrant'=>'Cadet already has a valid warrant for this course.']);
        if ($cadet->courses()->where('course_id',$course->id)->wherePivotIn('status',['enrolled','paid','passed'])->exists()) throw ValidationException::withMessages(['course'=>'Cadet already has an active enrollment for this course.']);
        $cadet->courses()->attach($course->id,['status'=>'enrolled']);
    }

    public function verifyPayment(Cadet $cadet, Course $course, string $reference): void
    {
        $this->assertInstructorCourse($course);
        $pivot=$cadet->courses()->where('course_id',$course->id)->first();
        if (!$pivot || $pivot->pivot->status!=='enrolled') throw ValidationException::withMessages(['course'=>'No payable course enrollment was found.']);
        $cadet->courses()->updateExistingPivot($course->id,['status'=>'paid','payment_reference'=>$reference,'paid_at'=>now()]);
    }

    public function recordResult(Cadet $cadet, Course $course, bool $passed): void
    {
        $this->assertInstructorCourse($course);
        $pivot=$cadet->courses()->where('course_id',$course->id)->first();
        if (!$pivot || $pivot->pivot->status!=='paid') throw ValidationException::withMessages(['course'=>'Payment must be verified before recording the result.']);
        $cadet->courses()->updateExistingPivot($course->id,['status'=>$passed?'passed':'failed','completed_at'=>today(),'result'=>$passed?'pass':'fail']);
    }

    public function issueWarrant(Cadet $cadet, Course $course, int $validityMonths=24): Warrant
    {
        $this->assertInstructorCourse($course);
        if (!$this->hasValidPassedCourse($cadet,$course)) throw ValidationException::withMessages(['warrant'=>'The cadet must pass and pay for the course before a warrant is issued.']);
        if ($this->hasValidWarrantForCourse($cadet,$course)) throw ValidationException::withMessages(['warrant'=>'A valid warrant already exists for this course.']);
        return DB::transaction(function()use($cadet,$course,$validityMonths):Warrant{
            $warrant=Warrant::create(['cadet_id'=>$cadet->service_number,'course_id'=>$course->id,'warrant_number'=>$this->generateWarrantNumber(),'type'=>'instructor','issued_at'=>today(),'expires_at'=>Carbon::today()->addMonths($validityMonths),'status'=>'active']);
            $cadet->instructor()->updateOrCreate([],['status'=>'active']);
            return $warrant;
        });
    }

    public function expireWarrants(): int
    {
        $expired=Warrant::where('status','active')->whereDate('expires_at','<=',today())->update(['status'=>'expired']);
        $this->syncInstructorStatuses();
        return $expired;
    }

    private function hasValidPassedCourse(Cadet $cadet, Course $course): bool
    {
        $pivot=$cadet->courses()->where('course_id',$course->id)->first();
        return (bool)$pivot && $pivot->pivot->status==='passed' && filled($pivot->pivot->payment_reference);
    }

    private function assertInstructorCourse(Course $course): void
    {
        if (!$course->is_instructor_course || $course->status !== 'active') throw ValidationException::withMessages(['course'=>'The selected course is not an active instructor course.']);
    }

    private function generateWarrantNumber(): string
    {
        do {$number='NACO-WR-'.now()->format('YmdHis').'-'.strtoupper(bin2hex(random_bytes(3)));} while(Warrant::where('warrant_number',$number)->exists());
        return $number;
    }

    private function syncInstructorStatuses(): void
    {
        foreach (\App\Models\Instructor::with('cadet')->get() as $instructor) $instructor->update(['status'=>$instructor->cadet?->hasValidWarrant()?'active':'expired']);
    }
}
