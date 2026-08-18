<?php

declare(strict_types=1);
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\PostAssignment;
use Illuminate\Http\Request;
class PostAssignmentController extends Controller
{
 public function index(Request $r){$q=PostAssignment::with(['cadet','post'])->latest();if($r->filled('cadet_id'))$q->where('cadet_id',$r->integer('cadet_id'));if($r->filled('post_id'))$q->where('post_id',$r->integer('post_id'));return response()->json($q->paginate(20));}
 public function store(Request $r){$d=$r->validate(['cadet_id'=>'required|exists:cadets,id','post_id'=>'required|exists:posts,id','start_date'=>'required|date','end_date'=>'nullable|date|after_or_equal:start_date','status'=>'nullable|string|max:30']);$d['reference']='NACO-POST-'.now()->format('YmdHis').'-'.$d['cadet_id'];return response()->json(PostAssignment::create($d),201);}
 public function show(PostAssignment $postAssignment){return response()->json($postAssignment->load(['cadet','post']));}
 public function update(Request $r,PostAssignment $postAssignment){$d=$r->validate(['post_id'=>'sometimes|required|exists:posts,id','start_date'=>'sometimes|required|date','end_date'=>'nullable|date|after_or_equal:start_date','status'=>'nullable|string|max:30']);$postAssignment->update($d);return response()->json($postAssignment->fresh(['cadet','post']));}
 public function destroy(PostAssignment $postAssignment){$postAssignment->delete();return response()->noContent();}
}
