<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ward;
use Illuminate\Http\Request;

class WardController extends Controller
{
    private function canRead(Request $request): bool { $u=$request->user(); return $u->hasGlobalAccess()||$u->isStateController()||$u->isLgaChairman()||$u->isHcs(); }
    private function guardWrite(Request $request): void { abort_unless($request->user()->hasGlobalAccess(),403); }
    public function index(Request $request){abort_unless($this->canRead($request),403);$u=$request->user();$q=Ward::with('lga.state')->withCount('cadets')->orderBy('name');if($u->isHcs()&&!$u->hasGlobalAccess())$q->whereKey($u->ward_id);elseif($u->isLgaChairman()&&!$u->hasGlobalAccess())$q->where('lga_id',$u->lga_id);elseif($u->isStateController()&&!$u->hasGlobalAccess())$q->whereHas('lga',fn($l)=>$l->where('state_id',$u->state_id));elseif($request->filled('lga_id')&&$u->hasGlobalAccess())$q->where('lga_id',$request->integer('lga_id'));return response()->json($q->paginate(20));}
    public function store(Request $request){$this->guardWrite($request);$d=$request->validate(['lga_id'=>'required|exists:lgas,id','name'=>'required|string|max:100','code'=>'required|string|max:30|unique:wards,code']);return response()->json(Ward::create($d),201);}
    public function show(Request $request,Ward $ward){abort_unless($this->canRead($request),403);$u=$request->user();$allowed=$u->hasGlobalAccess()||($u->isHcs()&&(int)$u->ward_id===(int)$ward->id)||($u->isLgaChairman()&&(int)$u->lga_id===(int)$ward->lga_id)||($u->isStateController()&&(int)$u->state_id===(int)$ward->lga->state_id);abort_unless($allowed,403);return response()->json($ward->load(['lga.state','cadets']));}
    public function update(Request $request,Ward $ward){$this->guardWrite($request);$d=$request->validate(['lga_id'=>'sometimes|required|exists:lgas,id','name'=>'sometimes|required|string|max:100','code'=>'sometimes|required|string|max:30|unique:wards,code,'.$ward->id]);$ward->update($d);return response()->json($ward->fresh());}
    public function destroy(Request $request,Ward $ward){$this->guardWrite($request);abort_if($ward->cadets()->exists(),422,'A ward with assigned cadets cannot be deleted.');$ward->delete();return response()->noContent();}
}
