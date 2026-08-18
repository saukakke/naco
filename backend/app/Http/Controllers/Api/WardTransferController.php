<?php

declare(strict_types=1);
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Cadet;
use App\Models\WardTransfer;
use App\Services\WardTransferService;
use Illuminate\Http\Request;
class WardTransferController extends Controller
{
 public function index(Request $r){$q=WardTransfer::with(['cadet','fromWard.lga.state','toWard.lga.state'])->latest();if(!$r->user()->isAdmin())$q->whereHas('cadet',fn($x)=>$x->where('user_id',$r->user()->id));return response()->json($q->paginate(20));}
 public function store(Request $r,WardTransferService $service){$d=$r->validate(['cadet_id'=>'required|exists:cadets,id','to_ward_id'=>'required|exists:wards,id','reason'=>'nullable|string|max:2000']);$cadet=Cadet::findOrFail($d['cadet_id']);abort_unless($r->user()->isAdmin()||$cadet->user_id===$r->user()->id,403);return response()->json($service->apply($cadet,(int)$d['to_ward_id'],$d['reason']??null),201);}
 public function show(WardTransfer $transfer){return response()->json($transfer->load(['cadet','fromWard.lga.state','toWard.lga.state']));}
 public function release(Request $r,WardTransfer $transfer,WardTransferService $service){abort_unless($r->user()->isAdmin()||($r->user()->isHcs()&&$r->user()->ward_id===$transfer->from_ward_id),403);return response()->json($service->release($transfer,(int)$r->user()->id));}
 public function sourceLga(Request $r,WardTransfer $transfer,WardTransferService $service){abort_unless($r->user()->isAdmin()||($r->user()->isLgaChairman()&&$r->user()->lga_id===$transfer->fromWard->lga_id),403);return response()->json($service->sourceLga($transfer,(int)$r->user()->id));}
 public function sourceState(Request $r,WardTransfer $transfer,WardTransferService $service){abort_unless($r->user()->isAdmin()||($r->user()->isStateController()&&$r->user()->state_id===$transfer->fromWard->lga->state_id),403);return response()->json($service->sourceState($transfer,(int)$r->user()->id));}
 public function destinationHcs(Request $r,WardTransfer $transfer,WardTransferService $service){abort_unless($r->user()->isAdmin()||($r->user()->isHcs()&&$r->user()->ward_id===$transfer->to_ward_id),403);return response()->json($service->destinationHcs($transfer,(int)$r->user()->id));}
 public function destinationLga(Request $r,WardTransfer $transfer,WardTransferService $service){abort_unless($r->user()->isAdmin()||($r->user()->isLgaChairman()&&$r->user()->lga_id===$transfer->toWard->lga_id),403);return response()->json($service->destinationLga($transfer,(int)$r->user()->id));}
 public function destinationState(Request $r,WardTransfer $transfer,WardTransferService $service){abort_unless($r->user()->isAdmin()||($r->user()->isStateController()&&$r->user()->state_id===$transfer->toWard->lga->state_id),403);return response()->json($service->destinationState($transfer,(int)$r->user()->id));}
 public function nationalApprove(Request $r,WardTransfer $transfer,WardTransferService $service){abort_unless($r->user()->isAdmin()||$r->user()->isNational(),403);return response()->json($service->nationalApprove($transfer,(int)$r->user()->id));}
}
