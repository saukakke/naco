<?php

declare(strict_types=1);
namespace App\Http\Controllers\Portal;
use App\Http\Controllers\Controller;
use App\Models\Cadet;
use App\Models\Ward;
use App\Models\WardTransfer;
use App\Services\WardTransferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
class WardTransferController extends Controller {
 public function index(Request $request):View { $u=$request->user(); $q=WardTransfer::with(['cadet','fromWard.lga.state','toWard.lga.state'])->latest(); if(!$u->isAdmin()) { if($u->cadet_id)$q->where('cadet_id',$u->cadet_id); else $q->whereRaw('1=0'); } return view('portal.ward-transfers.index',['transfers'=>$q->paginate(15),'user'=>$u]); }
 public function create(Request $request):View { $c=$request->user()->cadet; abort_unless($c,403); $c->load('ward.lga.state'); return view('portal.ward-transfers.create',['cadet'=>$c,'wards'=>Ward::with('lga.state')->where('id','!=',$c->ward_id)->orderBy('name')->get()]); }
 public function store(Request $request,WardTransferService $service):RedirectResponse { $c=$request->user()->cadet; abort_unless($c,403); $d=$request->validate(['to_ward_id'=>['required','exists:wards,id'],'reason'=>['nullable','string','max:2000']]); $service->apply($c,(int)$d['to_ward_id'],$d['reason']??null); return redirect()->route('portal.ward-transfers.index')->with('success','Ward transfer application submitted.'); }
 public function action(Request $request,WardTransfer $transfer,string $action,WardTransferService $service):RedirectResponse { $u=$request->user(); abort_unless($this->allowed($u,$transfer,$action),403); $map=['release'=>'release','source-lga'=>'sourceLga','source-state'=>'sourceState','destination-accept'=>'destinationHcs','destination-lga'=>'destinationLga','destination-state'=>'destinationState','national-approve'=>'nationalApprove']; abort_unless(isset($map[$action]),404); $service->{$map[$action]}($transfer,(int)$u->id); return back()->with('success','Ward transfer approval recorded.'); }
 private function allowed($u,WardTransfer $t,string $a):bool { $t->loadMissing('fromWard.lga','toWard.lga'); if($u->isAdmin())return true; $roles=['release'=>'hcs','source-lga'=>'chairman_self_reliance','source-state'=>'state_controller','destination-accept'=>'hcs','destination-lga'=>'chairman_self_reliance','destination-state'=>'state_controller','national-approve'=>'national']; if(($u->role??'')!==$roles[$a])return false; return match($a){'release'=>$u->ward_id===$t->from_ward_id,'destination-accept'=>$u->ward_id===$t->to_ward_id,'source-lga'=>$u->lga_id===$t->fromWard->lga_id,'destination-lga'=>$u->lga_id===$t->toWard->lga_id,'source-state'=>$u->state_id===$t->fromWard->lga->state_id,'destination-state'=>$u->state_id===$t->toWard->lga->state_id,'national-approve'=>true,default=>false}; }
}
