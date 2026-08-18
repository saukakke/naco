<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Cadet;
use App\Models\Course;
use App\Services\InstructorQualificationService;
use Illuminate\Http\Request;
class InstructorController extends Controller
{
 public function show(Cadet $cadet){return response()->json(['cadet'=>$cadet->load('instructor'),'is_instructor'=>$cadet->isInstructor(),'valid_warrant'=>$cadet->warrants()->where('status','active')->latest('expires_at')->first()]);}
 public function enroll(Request $request,Cadet $cadet,Course $course,InstructorQualificationService $service){$service->enroll($cadet,$course);return response()->json(['message'=>'Course enrollment created.','cadet'=>$cadet->fresh()->load('courses')],201);}
 public function payment(Request $request,Cadet $cadet,Course $course,InstructorQualificationService $service){$data=$request->validate(['payment_reference'=>'required|string|max:150']);$service->verifyPayment($cadet,$course,$data['payment_reference']);return response()->json(['message'=>'Course payment verified.']);}
 public function result(Request $request,Cadet $cadet,Course $course,InstructorQualificationService $service){$data=$request->validate(['passed'=>'required|boolean']);$service->recordResult($cadet,$course,$data['passed']);return response()->json(['message'=>'Course result recorded.']);}
 public function issueWarrant(Request $request,Cadet $cadet,Course $course,InstructorQualificationService $service){$data=$request->validate(['validity_months'=>'nullable|integer|min:1|max:60']);$w=$service->issueWarrant($cadet,$course,$data['validity_months']??24);return response()->json($w,201);}
}
