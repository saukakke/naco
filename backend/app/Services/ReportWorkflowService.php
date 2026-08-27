<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\FourMonthlyReport;
use App\Models\ReportPeriod;
use App\Models\ReportReview;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReportWorkflowService
{
    public function __construct(private PersonnelService $personnel){}
    public function periodFor(?\DateTimeInterface $date=null):ReportPeriod{$date=$date?now()->setTimestamp($date->getTimestamp()):now();$period=intdiv(((int)$date->format('n')-1),4)+1;$year=(int)$date->format('Y');$startMonth=($period-1)*4+1;$endMonth=$period*4;return ReportPeriod::firstOrCreate(['year'=>$year,'period'=>$period],['starts_on'=>sprintf('%04d-%02d-01',$year,$startMonth),'ends_on'=>date('Y-m-t',strtotime(sprintf('%04d-%02d-01',$year,$endMonth))),'due_on'=>date('Y-m-d',strtotime(sprintf('%04d-%02d-10',$year,$endMonth+1)))]);}
    public function submit(FourMonthlyReport $report,$user):void{if(!in_array($report->status,['draft','returned_by_lga'],true))throw ValidationException::withMessages(['report'=>'This report cannot be submitted in its current status.']);$report->update(['status'=>'submitted_to_lga','submitted_by'=>$user->id,'submitted_at'=>now()]);$this->notifyLga($report);}
    public function review(FourMonthlyReport $report,$user,string $level,string $action,?string $comments=null):void{DB::transaction(function()use($report,$user,$level,$action,$comments){$this->assertScope($report,$user,$level);$expected=['lga'=>['submitted_to_lga','returned_by_lga'],'state'=>['approved_by_lga','returned_by_state'],'national'=>['approved_by_state','returned_by_national']];if(!isset($expected[$level])||!in_array($report->status,$expected[$level],true))throw ValidationException::withMessages(['report'=>'This report is not available for this review level.']);$status=$action==='return'?"returned_by_{$level}":['lga'=>'approved_by_lga','state'=>'approved_by_state','national'=>'finalized'][$level];$report->update(['status'=>$status,'finalized_at'=>$status==='finalized'?now():null]);ReportReview::create(['report_id'=>$report->id,'reviewer_id'=>$user->id,'level'=>$level,'action'=>$action,'comments'=>$comments,'reviewed_at'=>now()]);if($action==='approve')$this->notifyNextLevel($report,$level);else $this->personnel->notify($report->submitter->id,'report.returned','4-Monthly Report Returned','Your report was returned for correction',['report_id'=>$report->id,'level'=>$level,'comments'=>$comments]);});}
    private function assertScope(FourMonthlyReport $report,$user,string $level):void{if($level==='lga')abort_unless($user->isLgaChairman()&&$user->lga_id===$report->ward->lga_id,403);elseif($level==='state')abort_unless($user->isStateController()&&$user->state_id===$report->ward->lga->state_id,403);elseif($level==='national')abort_unless($user->isNational()||$user->hasGlobalAccess(),403);else abort(403);}
    private function notifyLga(FourMonthlyReport $report):void{$users=\App\Models\User::where('role','chairman_self_reliance')->where('lga_id',$report->ward->lga_id)->get();foreach($users as $user)$this->personnel->notify($user->id,'report.submitted','4-Monthly Report Submitted','A ward report has been submitted for LGA review',['report_id'=>$report->id]);}
    private function notifyNextLevel(FourMonthlyReport $report,string $level):void{$users=$level==='lga'?\App\Models\User::where('state_id',$report->ward->lga->state_id)->get():\App\Models\User::query()->get();foreach($users as $user){$send=$level==='lga'?$user->isStateController():($user->isNational()||$user->hasGlobalAccess());if($send)$this->personnel->notify($user->id,'report.forwarded','4-Monthly Report Awaiting Review','A four-monthly report has been forwarded for your review',['report_id'=>$report->id,'level'=>$level]);}}
}
