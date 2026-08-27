<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    private function canRead(Request $request): bool { $u=$request->user(); return $u->hasGlobalAccess()||$u->isStateController()||$u->isLgaChairman()||$u->isHcs(); }
    private function guardWrite(Request $request): void { abort_unless($request->user()->hasGlobalAccess(),403); }
    public function index(Request $request){abort_unless($this->canRead($request),403);$u=$request->user();$q=State::withCount('lgas')->orderBy('name');if(!$u->hasGlobalAccess()){$stateId=$u->state_id??$u->lga?->state_id??$u->ward?->lga?->state_id;abort_unless($stateId,403);$q->whereKey($stateId);}return response()->json($q->paginate(20));}
    public function store(Request $request){$this->guardWrite($request);$d=$request->validate(['name'=>'required|string|max:100','code'=>'required|string|max:20|unique:states,code']);return response()->json(State::create($d),201);}
    public function show(Request $request,State $state){abort_unless($this->canRead($request),403);$u=$request->user();if(!$u->hasGlobalAccess()){ $stateId=$u->state_id??$u->lga?->state_id??$u->ward?->lga?->state_id;abort_unless((int)$stateId===(int)$state->id,403);}return response()->json($state->load('lgas'));}
    public function update(Request $request,State $state){$this->guardWrite($request);$d=$request->validate(['name'=>'sometimes|required|string|max:100','code'=>'sometimes|required|string|max:20|unique:states,code,'.$state->id]);$state->update($d);return response()->json($state->fresh());}
    public function destroy(Request $request,State $state){$this->guardWrite($request);abort_if($state->lgas()->exists(),422,'A state with LGAs cannot be deleted.');$state->delete();return response()->noContent();}
}
