<?php

declare(strict_types=1);

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Ward;
use App\Models\WardTransfer;
use App\Services\AuthorizationService;
use App\Services\WardTransferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WardTransferController extends Controller
{
    public function index(Request $request,AuthorizationService $authorization): View { $query=$authorization->wardTransferQuery($request->user())->with(['cadet','fromWard.lga.state','toWard.lga.state'])->latest(); return view('portal.ward-transfers.index',['transfers'=>$query->paginate(15),'user'=>$request->user()]); }
    public function create(Request $request): View { $cadet=$request->user()->cadet; abort_unless($cadet,403); $cadet->load('ward.lga.state'); return view('portal.ward-transfers.create',['cadet'=>$cadet,'wards'=>Ward::with('lga.state')->when($cadet->ward_id,fn($q)=>$q->where('id','!=',$cadet->ward_id))->orderBy('name')->get()]); }
    public function store(Request $request,WardTransferService $service): RedirectResponse { $cadet=$request->user()->cadet; abort_unless($cadet,403); $data=$request->validate(['to_ward_id'=>['required','exists:wards,id'],'reason'=>['nullable','string','max:2000']]); $service->apply($cadet,(int)$data['to_ward_id'],$data['reason']??null); return redirect()->route('portal.ward-transfers.index')->with('success','Ward transfer application submitted.'); }
    public function action(Request $request,WardTransfer $transfer,string $action,WardTransferService $service): RedirectResponse { $user=$request->user(); abort_unless($this->allowed($user,$transfer,$action),403); $map=['release'=>'release','source-lga'=>'sourceLga','source-state'=>'sourceState','destination-accept'=>'destinationHcs','destination-lga'=>'destinationLga','destination-state'=>'destinationState','national-approve'=>'nationalApprove']; abort_unless(isset($map[$action]),404); $service->{$map[$action]}($transfer,(int)$user->id); return back()->with('success','Ward transfer approval recorded.'); }
    private function allowed($user,WardTransfer $transfer,string $action):bool { if($user->hasGlobalAccess())return true; $transfer->loadMissing('fromWard.lga','toWard.lga'); return match($action){'release'=>$user->isHcs()&&(int)$user->ward_id===(int)$transfer->from_ward_id,'source-lga'=>$user->isLgaChairman()&&(int)$user->lga_id===(int)$transfer->fromWard->lga_id,'source-state'=>$user->isStateController()&&(int)$user->state_id===(int)$transfer->fromWard->lga->state_id,'destination-accept'=>$user->isHcs()&&(int)$user->ward_id===(int)$transfer->to_ward_id,'destination-lga'=>$user->isLgaChairman()&&(int)$user->lga_id===(int)$transfer->toWard->lga_id,'destination-state'=>$user->isStateController()&&(int)$user->state_id===(int)$transfer->toWard->lga->state_id,'national-approve'=>$user->isNational(),default=>false}; }
}
