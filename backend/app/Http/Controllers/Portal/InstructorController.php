<?php

declare(strict_types=1);
namespace App\Http\Controllers\Portal;
use App\Http\Controllers\Controller;
use App\Models\Cadet;
use App\Models\Course;
use App\Models\Warrant;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
class InstructorController extends Controller
{
 public function index(Request $request): View { $q=Cadet::with(['unit','warrants'=>fn($x)=>$x->latest()]); if(!$request->user()->isAdmin()) $q->where('id',$request->user()->cadet?->id); return view('portal.instructors.index',['cadets'=>$q->paginate(15)]); }
 public function courses(): View { return view('portal.instructors.courses',['courses'=>Course::where('is_instructor_course',true)->orderBy('name')->get()]); }
 public function enroll(Request $request, Course $course): RedirectResponse { $cadet=$request->user()->cadet; abort_unless($cadet && $course->is_instructor_course,403); abort_unless($course->fee >= 0,422); $cadet->courses()->syncWithoutDetaching([$course->id=>['status'=>'enrolled']]); return back()->with('success','Instructor course enrollment created.'); }
 public function showWarrant(Request $request, Warrant $warrant): View { abort_unless($request->user()->isAdmin() || $warrant->cadet->user_id===$request->user()->id,403); return view('portal.instructors.warrant',['warrant'=>$warrant->load(['cadet','course'])]); }
}
