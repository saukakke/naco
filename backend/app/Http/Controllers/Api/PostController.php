<?php

declare(strict_types=1);
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
class PostController extends Controller
{
 public function index(Request $r){$q=Post::withCount('assignments');if($r->filled('level'))$q->where('level',$r->string('level'));return response()->json($q->orderBy('level')->orderBy('name')->paginate(20));}
 public function store(Request $r){$d=$r->validate(['name'=>'required|string|max:150','slug'=>'required|string|max:180|unique:posts,slug','level'=>'required|string|max:30','description'=>'nullable|string']);return response()->json(Post::create($d),201);}
 public function show(Post $post){return response()->json($post->load('assignments.cadet'));}
 public function update(Request $r,Post $post){$d=$r->validate(['name'=>'sometimes|required|string|max:150','slug'=>'sometimes|required|string|max:180|unique:posts,slug,'.$post->id,'level'=>'sometimes|required|string|max:30','description'=>'nullable|string']);$post->update($d);return response()->json($post->fresh());}
 public function destroy(Post $post){abort_if($post->assignments()->exists(),422,'A post with assignments cannot be deleted.');$post->delete();return response()->noContent();}
}
