<?php

declare(strict_types=1);
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Rank;
use Illuminate\Http\Request;
class RankController extends Controller
{
 public function index(Request $r){$q=Rank::with('category');if($r->filled('rank_category_id'))$q->where('rank_category_id',$r->integer('rank_category_id'));return response()->json($q->orderBy('order')->paginate(30));}
 public function store(Request $r){$d=$r->validate(['rank_category_id'=>'required|exists:rank_categories,id','name'=>'required|string|max:100','slug'=>'required|string|max:120|unique:ranks,slug','order'=>'nullable|integer|min:0']);return response()->json(Rank::create($d),201);}
 public function show(Rank $rank){return response()->json($rank->load(['category','cadets']));}
 public function update(Request $r,Rank $rank){$d=$r->validate(['rank_category_id'=>'sometimes|required|exists:rank_categories,id','name'=>'sometimes|required|string|max:100','slug'=>'sometimes|required|string|max:120|unique:ranks,slug,'.$rank->id,'order'=>'nullable|integer|min:0']);$rank->update($d);return response()->json($rank->fresh());}
 public function destroy(Rank $rank){abort_if($rank->cadets()->exists(),422,'A rank assigned to cadets cannot be deleted.');$rank->delete();return response()->noContent();}
}
