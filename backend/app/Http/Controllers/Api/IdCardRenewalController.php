<?php

declare(strict_types=1);
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\IdCardRenewalApplication;
use App\Services\IdCardRenewalService;
use Illuminate\Http\Request;
class IdCardRenewalController extends Controller
{
 public function index(Request $r){$q=IdCardRenewalApplication::with('cadet')->latest();if(!$r->user()->isAdmin())$q->whereHas('cadet',fn($x)=>$x->where('user_id',$r->user()->id));return response()->json($q->paginate(20));}
 public function store(Request $r,IdCardRenewalService $service){$cadet=$r->user()->cadet;abort_unless($cadet,403);$d=$r->validate(['reason'=>'nullable|string|max:2000']);return response()->json($service->apply($cadet,$d['reason']??null),201);}
 public function show(IdCardRenewalApplication $application){return response()->json($application->load('cadet'));}
 public function verifyPayment(Request $r,IdCardRenewalApplication $application,IdCardRenewalService $service){abort_unless($r->user()->isAdmin(),403);$d=$r->validate(['payment_reference'=>'required|string|max:150']);return response()->json($service->verifyPayment($application,$d['payment_reference']));}
 public function approve(Request $r,IdCardRenewalApplication $application,IdCardRenewalService $service){abort_unless($r->user()->isAdmin(),403);return response()->json($service->approve($application,(int)$r->user()->id));}
 public function issue(Request $r,IdCardRenewalApplication $application,IdCardRenewalService $service){abort_unless($r->user()->isAdmin(),403);return response()->json($service->issue($application));}
}
