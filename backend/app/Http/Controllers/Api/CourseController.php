<?php

declare(strict_types=1);
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
class CourseController extends Controller
{
 public function index(){return response()->json(Course::latest()->paginate(20));}
 public function store(Request $r){$d=$r->validate(['code'=>'required|string|max:50|unique:courses,code','name'=>'required|string|max:150','description'=>'nullable|string','duration_days'=>'nullable|integer|min:1','status'=>'nullable|string|max:30']);return response()->json(Course::create($d),201);}
 public function show(Course $course){return response()->json($course->load('cadets'));}
 public function update(Request $r,Course $course){$d=$r->validate(['code'=>'sometimes|required|string|max:50|unique:courses,code,'.$course->id,'name'=>'sometimes|required|string|max:150','description'=>'nullable|string','duration_days'=>'nullable|integer|min:1','status'=>'nullable|string|max:30']);$course->update($d);return response()->json($course->fresh());}
 public function destroy(Course $course){$course->delete();return response()->noContent();}
}
