<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PostAssignment;
use Illuminate\Http\Request;

class PostAssignmentController extends Controller
{
    private function guard(Request $request): void { abort_unless($request->user()->hasGlobalAccess(), 403); }
    public function index(Request $request){$this->guard($request);$q=PostAssignment::with(['cadet','post'])->latest();if($request->filled('cadet_id'))$q->where('cadet_id',$request->string('cadet_id'));if($request->filled('post_id'))$q->where('post_id',$request->integer('post_id'));return response()->json($q->paginate(20));}
    public function store(Request $request){$this->guard($request);$d=$request->validate(['cadet_id'=>'required|exists:cadets,service_number','post_id'=>'required|exists:posts,id','start_date'=>'required|date','end_date'=>'nullable|date|after_or_equal:start_date','status'=>'nullable|in:active,inactive']);$d['reference']='NACO-POST-'.now()->format('YmdHis').'-'.bin2hex(random_bytes(3));return response()->json(PostAssignment::create($d),201);}
    public function show(Request $request,PostAssignment $postAssignment){$this->guard($request);return response()->json($postAssignment->load(['cadet','post']));}
    public function update(Request $request,PostAssignment $postAssignment){$this->guard($request);$d=$request->validate(['post_id'=>'sometimes|required|exists:posts,id','start_date'=>'sometimes|required|date','end_date'=>'nullable|date|after_or_equal:start_date','status'=>'nullable|in:active,inactive']);$postAssignment->update($d);return response()->json($postAssignment->fresh(['cadet','post']));}
    public function destroy(Request $request,PostAssignment $postAssignment){$this->guard($request);$postAssignment->delete();return response()->noContent();}
}
