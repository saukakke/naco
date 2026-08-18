<?php

declare(strict_types=1);
namespace App\Services;
use App\Models\Cadet;
use App\Models\WardTransfer;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class WardTransferService {
 public function apply(Cadet $cadet,int $toWardId,?string $reason=null):WardTransfer { $cadet->load('ward.lga.state'); \App\Models\Ward::with('lga.state')->findOrFail($toWardId); if(!$cadet->ward_id)throw ValidationException::withMessages(['ward'=>'The cadet has no current ward assigned.']); if((int)$cadet->ward_id===$toWardId)throw ValidationException::withMessages(['to_ward_id'=>'The destination ward must be different.']); if(WardTransfer::where('cadet_id',$cadet->id)->whereNotIn('status',['completed','rejected'])->exists())throw ValidationException::withMessages(['transfer'=>'An active ward transfer already exists.']); return WardTransfer::create(['cadet_id'=>$cadet->id,'from_ward_id'=>$cadet->ward_id,'to_ward_id'=>$toWardId,'reference'=>'NACO-WT-'.now()->format('YmdHis').'-'.$cadet->id,'status'=>'pending_source_hcs','reason'=>$reason]); }
 public function release(WardTransfer $t,int $userId):WardTransfer{$this->state($t,'pending_source_hcs');$this->update($t,['status'=>'pending_source_lga','source_hcs_released_at'=>now(),'source_hcs_id'=>$userId]);return $t->fresh();}
 public function sourceLga(WardTransfer $t,int $userId):WardTransfer{$this->state($t,'pending_source_lga');$this->update($t,['status'=>'pending_source_state','source_lga_acknowledged_at'=>now(),'source_lga_acknowledged_by'=>$userId]);return $t->fresh();}
 public function sourceState(WardTransfer $t,int $userId):WardTransfer{$this->state($t,'pending_source_state');$this->update($t,['status'=>'pending_destination_hcs','source_state_acknowledged_at'=>now(),'source_state_acknowledged_by'=>$userId]);return $t->fresh();}
 public function destinationHcs(WardTransfer $t,int $userId):WardTransfer{$this->state($t,'pending_destination_hcs');$next=$this->destinationLgaRequired($t)?'pending_destination_lga':'pending_national';$this->update($t,['status'=>$next,'destination_hcs_accepted_at'=>now(),'destination_hcs_id'=>$userId]);return $t->fresh();}
 public function destinationLga(WardTransfer $t,int $userId):WardTransfer{$this->state($t,'pending_destination_lga');$next=$this->destinationStateRequired($t)?'pending_destination_state':'pending_national';$this->update($t,['status'=>$next,'destination_lga_acknowledged_at'=>now(),'destination_lga_acknowledged_by'=>$userId]);return $t->fresh();}
 public function destinationState(WardTransfer $t,int $userId):WardTransfer{$this->state($t,'pending_destination_state');$this->update($t,['status'=>'pending_national','destination_state_acknowledged_at'=>now(),'destination_state_acknowledged_by'=>$userId]);return $t->fresh();}
 public function nationalApprove(WardTransfer $t,int $userId):WardTransfer{$this->state($t,'pending_national');return DB::transaction(function()use($t,$userId){$t->cadet()->update(['ward_id'=>$t->to_ward_id]);$this->update($t,['status'=>'completed','national_approved_at'=>now(),'national_approved_by'=>$userId,'completed_at'=>now()]);return $t->fresh();});}
 private function state(WardTransfer $t,string $expected):void{if($t->status!==$expected)throw ValidationException::withMessages(['transfer'=>'This approval is not the current step in the transfer workflow.']);}
 private function update(WardTransfer $t,array $data):void{$t->update($data);}
 private function destinationLgaRequired(WardTransfer $t):bool{$t->loadMissing('fromWard.lga','toWard.lga');return $t->fromWard->lga_id!==$t->toWard->lga_id;}
 private function destinationStateRequired(WardTransfer $t):bool{$t->loadMissing('fromWard.lga','toWard.lga');return $t->fromWard->lga_id!==$t->toWard->lga_id;}
}
