<?php

declare(strict_types=1);
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\State;
use Illuminate\Http\Request;
class StateController extends Controller
{
 public function index(){return response()->json(State::withCount('lgas')->orderBy('name')->paginate(20));}
 public function store(Request $r){$d=$r->validate(['name'=>'required|string|max:100','code'=>'required|string|max:20|unique:states,code']);return response()->json(State::create($d),201);}
 public function show(State $state){return response()->json($state->load('lgas'));}
 public function update(Request $r,State $state){$d=$r->validate(['name'=>'sometimes|required|string|max:100','code'=>'sometimes|required|string|max:20|unique:states,code,'.$state->id]);$state->update($d);return response()->json($state->fresh());}
 public function destroy(State $state){abort_if($state->lgas()->exists(),422,'A state with LGAs cannot be deleted.');$state->delete();return response()->noContent();}
}
