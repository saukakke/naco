<?php

declare(strict_types=1);

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Cadet;
use App\Models\IdCardRenewalApplication;
use App\Services\IdCardRenewalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IdCardRenewalController extends Controller
{
    public function index(Request $request): View { $q=IdCardRenewalApplication::with('cadet')->latest(); if(!$request->user()->hasGlobalAccess()) $q->where('cadet_id',$request->user()->cadet_id ?: '__none__'); return view('portal.id-card-renewals.index',['applications'=>$q->paginate(15),'user'=>$request->user()]); }
    public function create(Request $request): View { $cadet=$request->user()->cadet; abort_unless($cadet && app(IdCardRenewalService::class)->eligible($cadet),403,'Your ID card is not yet within the two-month renewal window.'); return view('portal.id-card-renewals.create',compact('cadet')); }
    public function store(Request $request,IdCardRenewalService $service): RedirectResponse { $cadet=$request->user()->cadet; abort_unless($cadet,403); $data=$request->validate(['reason'=>['nullable','string','max:2000']]); $service->apply($cadet,$data['reason']??null); return redirect()->route('portal.id-card-renewals.index')->with('success','ID card renewal application submitted.'); }
    public function verifyPayment(Request $request,IdCardRenewalApplication $application,IdCardRenewalService $service): RedirectResponse { abort_unless($request->user()->hasGlobalAccess(),403); $data=$request->validate(['payment_reference'=>['required','string','max:150']]); $service->verifyPayment($application,$data['payment_reference']); return back()->with('success','ID card renewal payment verified.'); }
    public function approve(Request $request,IdCardRenewalApplication $application,IdCardRenewalService $service): RedirectResponse { abort_unless($request->user()->hasGlobalAccess(),403); $service->approve($application,(int)$request->user()->id); return back()->with('success','ID card renewal approved.'); }
    public function issue(Request $request,IdCardRenewalApplication $application,IdCardRenewalService $service): RedirectResponse { abort_unless($request->user()->hasGlobalAccess(),403); $service->issue($application); return back()->with('success','New ID card issued and expiry date renewed.'); }
}
