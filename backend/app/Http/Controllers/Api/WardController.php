<?php

declare(strict_types=1);
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Ward;
use Illuminate\Http\Request;
class WardController extends Controller
{
 public function index(Request $r){$q=Ward::with('lga.state')->withCount('cadets');if($r->filled('lga_id'))$q->where('lga_id',$r->integer('lga_id'));return response()->json($q->orderBy('name')->paginate(20));}
 public function store(Request $r){$d=$r->validate(['lga_id'=>'required|exists:lgas,id','name'=>'required|string|max:100','code'=>'required|string|max:30|unique:wards,code']);return response()->json(Ward::create($d),201);}
 public function show(Ward $ward){return response()->json($ward->load(['lga.state','cadets']));}
 public function update(Request $r,Ward $ward){$d=$r->validate(['lga_id'=>'sometimes|required|exists:lgas,id','name'=>'sometimes|required|string|max:100','code'=>'sometimes|required|string|max:30|unique:wards,code,'.$ward->id]);$ward->update($d);return response()->json($ward->fresh());}
 public function destroy(Ward $ward){abort_if($ward->cadets()->exists(),422,'A ward with assigned cadets cannot be deleted.');$ward->delete();return response()->noContent();}
}
