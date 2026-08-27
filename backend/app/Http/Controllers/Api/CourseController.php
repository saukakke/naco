<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    private function guard(Request $request): void { abort_unless($request->user()->hasGlobalAccess(), 403); }

    public function index(Request $request){ $this->guard($request); return response()->json(Course::latest()->paginate(20)); }
    public function store(Request $request){ $this->guard($request); $data=$request->validate(['code'=>'required|string|max:50|unique:courses,code','name'=>'required|string|max:150','description'=>'nullable|string','duration_days'=>'nullable|integer|min:1','status'=>'nullable|in:active,inactive','fee'=>'nullable|numeric|min:0','is_instructor_course'=>'sometimes|boolean']); return response()->json(Course::create($data),201); }
    public function show(Request $request, Course $course){ $this->guard($request); return response()->json($course->load('cadets')); }
    public function update(Request $request, Course $course){ $this->guard($request); $data=$request->validate(['code'=>'sometimes|required|string|max:50|unique:courses,code,'.$course->id,'name'=>'sometimes|required|string|max:150','description'=>'nullable|string','duration_days'=>'nullable|integer|min:1','status'=>'nullable|in:active,inactive','fee'=>'nullable|numeric|min:0','is_instructor_course'=>'sometimes|boolean']); $course->update($data); return response()->json($course->fresh()); }
    public function destroy(Request $request, Course $course){ $this->guard($request); abort_if($course->cadets()->exists(),422,'A course with enrollments cannot be deleted.'); $course->delete(); return response()->noContent(); }
}
