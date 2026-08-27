<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rank;
use Illuminate\Http\Request;

class RankController extends Controller
{
    private function guard(Request $request): void { abort_unless($request->user()->hasGlobalAccess(), 403); }
    public function index(Request $request){$this->guard($request);$q=Rank::with('category');if($request->filled('rank_category_id'))$q->where('rank_category_id',$request->integer('rank_category_id'));return response()->json($q->orderBy('order')->paginate(30));}
    public function store(Request $request){$this->guard($request);$d=$request->validate(['rank_category_id'=>'required|exists:rank_categories,id','name'=>'required|string|max:100','slug'=>'required|string|max:120|unique:ranks,slug','order'=>'nullable|integer|min:0']);return response()->json(Rank::create($d),201);}
    public function show(Request $request,Rank $rank){$this->guard($request);return response()->json($rank->load(['category','cadets']));}
    public function update(Request $request,Rank $rank){$this->guard($request);$d=$request->validate(['rank_category_id'=>'sometimes|required|exists:rank_categories,id','name'=>'sometimes|required|string|max:100','slug'=>'sometimes|required|string|max:120|unique:ranks,slug,'.$rank->id,'order'=>'nullable|integer|min:0']);$rank->update($d);return response()->json($rank->fresh());}
    public function destroy(Request $request,Rank $rank){$this->guard($request);abort_if($rank->cadets()->exists(),422,'A rank assigned to cadets cannot be deleted.');$rank->delete();return response()->noContent();}
}
