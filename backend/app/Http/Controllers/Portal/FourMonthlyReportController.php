<?php

declare(strict_types=1);

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\FourMonthlyReport;
use App\Services\ReportWorkflowService;
use Illuminate\Http\Request;

class FourMonthlyReportController extends Controller
{
    public function index(Request $request){$u=$request->user();$q=FourMonthlyReport::with(['ward.lga.state','period'])->latest();if($u->isHcs())$q->where('submitted_by',$u->id);elseif($u->isLgaChairman()&&$u->lga_id)$q->whereHas('ward',fn($w)=>$w->where('lga_id',$u->lga_id));elseif($u->isStateController()&&$u->state_id)$q->whereHas('ward.lga',fn($l)=>$l->where('state_id',$u->state_id));elseif(!$u->hasGlobalAccess())$q->whereRaw('1=0');return view('portal.reports.index',['reports'=>$q->paginate(20)->withQueryString()]);}
    public function create(Request $request,ReportWorkflowService $service){abort_unless($request->user()->isHcs()&&$request->user()->ward_id,403);return view('portal.reports.create',['period'=>$service->periodFor(),'ward'=>$request->user()->ward]);}
    public function store(Request $request,ReportWorkflowService $service){$u=$request->user();abort_unless($u->isHcs()&&$u->ward_id,403);$data=$request->validate(['summary'=>'required|string','activities'=>'nullable|array','membership'=>'nullable|array','training'=>'nullable|array','self_reliance'=>'nullable|array','finance'=>'nullable|array']);$period=$service->periodFor();$report=FourMonthlyReport::firstOrCreate(['ward_id'=>$u->ward_id,'report_period_id'=>$period->id],array_merge($data,['status'=>'draft','submitted_by'=>$u->id]));abort_if($report->status!=='draft',422,'This period report already exists and is not editable.');$report->update($data);return redirect()->route('portal.reports.show',$report)->with('success','Report saved as draft.');}
    public function show(Request $request,FourMonthlyReport $report){$this->authorizeReport($request,$report);return view('portal.reports.show',['report'=>$report->load(['ward.lga.state','period','reviews.reviewer','attachments'])]);}
    public function submit(Request $request,FourMonthlyReport $report,ReportWorkflowService $service){abort_unless($request->user()->isHcs()&&$report->submitted_by===$request->user()->id&&$report->ward_id===$request->user()->ward_id,403);$service->submit($report,$request->user());return back()->with('success','Report submitted to Chairman Self-Reliance.');}
    public function review(Request $request,FourMonthlyReport $report,ReportWorkflowService $service){$data=$request->validate(['action'=>'required|in:approve,return','comments'=>'nullable|string|max:5000']);$u=$request->user();$level=$u->isLgaChairman()?'lga':($u->isStateController()?'state':($u->isNational()||$u->hasGlobalAccess()?'national':null));abort_unless($level,403);$service->review($report,$u,$level,$data['action'],$data['comments']??null);return back()->with('success','Report review recorded.');}
    private function authorizeReport(Request $request,FourMonthlyReport $report):void{$u=$request->user();$allowed=$u->hasGlobalAccess()||($u->isHcs()&&(int)$u->ward_id===(int)$report->ward_id)||($u->isLgaChairman()&&(int)$u->lga_id===(int)$report->ward->lga_id)||($u->isStateController()&&(int)$u->state_id===(int)$report->ward->lga->state_id);abort_unless($allowed,403);}
}
