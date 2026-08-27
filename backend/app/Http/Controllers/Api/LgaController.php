<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lga;
use Illuminate\Http\Request;

class LgaController extends Controller
{
    private function canRead(Request $request): bool { $u=$request->user(); return $u->hasGlobalAccess()||$u->isStateController()||$u->isLgaChairman()||$u->isHcs(); }
    private function guardWrite(Request $request): void { abort_unless($request->user()->hasGlobalAccess(),403); }
    public function index(Request $request){abort_unless($this->canRead($request),403);$u=$request->user();$q=Lga::with('state')->withCount('wards')->orderBy('name');if($u->isLgaChairman()&&!$u->hasGlobalAccess())$q->whereKey($u->lga_id);elseif($u->isHcs()&&!$u->hasGlobalAccess())$q->whereKey($u->ward?->lga_id);elseif($u->isStateController()&&!$u->hasGlobalAccess())$q->where('state_id',$u->state_id);elseif($request->filled('state_id')&&$u->hasGlobalAccess())$q->where('state_id',$request->integer('state_id'));return response()->json($q->paginate(20));}
    public function store(Request $request){$this->guardWrite($request);$d=$request->validate(['state_id'=>'required|exists:states,id','name'=>'required|string|max:100']);return response()->json(Lga::create($d),201);}
    public function show(Request $request,Lga $lga){abort_unless($this->canRead($request),403);$u=$request->user();$allowed=$u->hasGlobalAccess()||($u->isLgaChairman()&&(int)$u->lga_id===(int)$lga->id)||($u->isHcs()&&(int)$u->ward?->lga_id===(int)$lga->id)||($u->isStateController()&&(int)$u->state_id===(int)$lga->state_id);abort_unless($allowed,403);return response()->json($lga->load(['state','wards']));}
    public function update(Request $request,Lga $lga){$this->guardWrite($request);$d=$request->validate(['state_id'=>'sometimes|required|exists:states,id','name'=>'sometimes|required|string|max:100']);$lga->update($d);return response()->json($lga->fresh());}
    public function destroy(Request $request,Lga $lga){$this->guardWrite($request);abort_if($lga->wards()->exists(),422,'An LGA with wards cannot be deleted.');$lga->delete();return response()->noContent();}
}
