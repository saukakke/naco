<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    private function guard(Request $request): void { abort_unless($request->user()->hasGlobalAccess(), 403); }
    public function index(Request $request){$this->guard($request);$q=Post::withCount('assignments');if($request->filled('level'))$q->where('level',$request->string('level'));return response()->json($q->orderBy('level')->orderBy('name')->paginate(20));}
    public function store(Request $request){$this->guard($request);$d=$request->validate(['name'=>'required|string|max:150','slug'=>'required|string|max:180|unique:posts,slug','level'=>'required|in:national,state,lga,ward','description'=>'nullable|string']);return response()->json(Post::create($d),201);}
    public function show(Request $request,Post $post){$this->guard($request);return response()->json($post->load('assignments.cadet'));}
    public function update(Request $request,Post $post){$this->guard($request);$d=$request->validate(['name'=>'sometimes|required|string|max:150','slug'=>'sometimes|required|string|max:180|unique:posts,slug,'.$post->id,'level'=>'sometimes|required|in:national,state,lga,ward','description'=>'nullable|string']);$post->update($d);return response()->json($post->fresh());}
    public function destroy(Request $request,Post $post){$this->guard($request);abort_if($post->assignments()->exists(),422,'A post with assignments cannot be deleted.');$post->delete();return response()->noContent();}
}
