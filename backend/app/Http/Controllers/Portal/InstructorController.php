<?php

declare(strict_types=1);

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Cadet;
use App\Models\Course;
use App\Models\Warrant;
use App\Services\AuthorizationService;
use App\Services\InstructorQualificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InstructorController extends Controller
{
    public function index(Request $request,AuthorizationService $authorization): View { $q=$authorization->cadetQuery($request->user())->with(['unit','ward','warrants'=>fn($x)=>$x->latest()]); return view('portal.instructors.index',['cadets'=>$q->paginate(15)]); }
    public function courses(Request $request): View { abort_unless($request->user()->hasGlobalAccess()||$request->user()->isInstructor()||$request->user()->isCadet(),403); return view('portal.instructors.courses',['courses'=>Course::where('is_instructor_course',true)->where('status','active')->orderBy('name')->get()]); }
    public function enroll(Request $request,Course $course,InstructorQualificationService $service): RedirectResponse { $cadet=$request->user()->cadet; abort_unless($cadet,403); $service->enroll($cadet,$course); return back()->with('success','Instructor course enrollment created.'); }
    public function showWarrant(Request $request,Warrant $warrant,AuthorizationService $authorization): View { $warrant->loadMissing('cadet'); abort_unless($authorization->canAccessCadet($request->user(),$warrant->cadet),403); return view('portal.instructors.warrant',['warrant'=>$warrant->load(['cadet','course'])]); }
}
