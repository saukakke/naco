<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RankCategory;
use Illuminate\Http\Request;

class RankCategoryController extends Controller
{
    private function guard(Request $request): void { abort_unless($request->user()->hasGlobalAccess(), 403); }
    public function index(Request $request){$this->guard($request);return response()->json(RankCategory::with('ranks')->orderBy('order')->paginate(20));}
    public function store(Request $request){$this->guard($request);$d=$request->validate(['name'=>'required|string|max:100','slug'=>'required|string|max:120|unique:rank_categories,slug','order'=>'nullable|integer|min:0']);return response()->json(RankCategory::create($d),201);}
    public function show(Request $request,RankCategory $rankCategory){$this->guard($request);return response()->json($rankCategory->load('ranks'));}
    public function update(Request $request,RankCategory $rankCategory){$this->guard($request);$d=$request->validate(['name'=>'sometimes|required|string|max:100','slug'=>'sometimes|required|string|max:120|unique:rank_categories,slug,'.$rankCategory->id,'order'=>'nullable|integer|min:0']);$rankCategory->update($d);return response()->json($rankCategory->fresh());}
    public function destroy(Request $request,RankCategory $rankCategory){$this->guard($request);abort_if($rankCategory->ranks()->exists(),422,'A rank category with ranks cannot be deleted.');$rankCategory->delete();return response()->noContent();}
}
