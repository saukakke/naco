<?php

declare(strict_types=1);
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Lga;
use Illuminate\Http\Request;
class LgaController extends Controller
{
 public function index(Request $r){$q=Lga::with('state')->withCount('wards');if($r->filled('state_id'))$q->where('state_id',$r->integer('state_id'));return response()->json($q->orderBy('name')->paginate(20));}
 public function store(Request $r){$d=$r->validate(['state_id'=>'required|exists:states,id','name'=>'required|string|max:100']);return response()->json(Lga::create($d),201);}
 public function show(Lga $lga){return response()->json($lga->load(['state','wards']));}
 public function update(Request $r,Lga $lga){$d=$r->validate(['state_id'=>'sometimes|required|exists:states,id','name'=>'sometimes|required|string|max:100']);$lga->update($d);return response()->json($lga->fresh());}
 public function destroy(Lga $lga){abort_if($lga->wards()->exists(),422,'An LGA with wards cannot be deleted.');$lga->delete();return response()->noContent();}
}
