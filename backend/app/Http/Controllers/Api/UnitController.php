<?php

declare(strict_types=1);
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
class UnitController extends Controller
{
 public function index(){return response()->json(Unit::withCount('cadets')->orderBy('name')->paginate(20));}
 public function store(Request $r){$d=$r->validate(['code'=>'required|string|max:30|unique:units,code','name'=>'required|string|max:100']);return response()->json(Unit::create($d),201);}
 public function show(Unit $unit){return response()->json($unit->load('cadets'));}
 public function update(Request $r,Unit $unit){$d=$r->validate(['code'=>'sometimes|required|string|max:30|unique:units,code,'.$unit->id,'name'=>'sometimes|required|string|max:100']);$unit->update($d);return response()->json($unit->fresh());}
 public function destroy(Unit $unit){abort_if($unit->cadets()->exists(),422,'A unit with assigned cadets cannot be deleted.');$unit->delete();return response()->noContent();}
}
