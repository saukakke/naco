<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    private function canRead(Request $request,Unit $unit=null):bool{$u=$request->user();if($u->hasGlobalAccess())return true;if($u->isUnitCommander())return $u->unit_id!==null&&(!$unit||(int)$u->unit_id===(int)$unit->id);if($u->cadet)return !$unit||(int)$u->cadet->unit_id===(int)$unit->id;return false;}
    private function guardWrite(Request $request):void{abort_unless($request->user()->hasGlobalAccess(),403);}
    public function index(Request $request){abort_unless($this->canRead($request),403);$q=Unit::withCount('cadets')->orderBy('name');if(!$request->user()->hasGlobalAccess())$q->whereKey($request->user()->unit_id??$request->user()->cadet?->unit_id);return response()->json($q->paginate(20));}
    public function store(Request $request){$this->guardWrite($request);$d=$request->validate(['code'=>'required|string|max:30|unique:units,code','name'=>'required|string|max:100']);return response()->json(Unit::create($d),201);}
    public function show(Request $request,Unit $unit){abort_unless($this->canRead($request,$unit),403);return response()->json($unit->load('cadets'));}
    public function update(Request $request,Unit $unit){$this->guardWrite($request);$d=$request->validate(['code'=>'sometimes|required|string|max:30|unique:units,code,'.$unit->id,'name'=>'sometimes|required|string|max:100']);$unit->update($d);return response()->json($unit->fresh());}
    public function destroy(Request $request,Unit $unit){$this->guardWrite($request);abort_if($unit->cadets()->exists(),422,'A unit with assigned cadets cannot be deleted.');$unit->delete();return response()->noContent();}
}
