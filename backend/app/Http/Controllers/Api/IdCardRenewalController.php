<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IdCardRenewalApplication;
use App\Services\AuthorizationService;
use App\Services\IdCardRenewalService;
use Illuminate\Http\Request;

class IdCardRenewalController extends Controller
{
    public function index(Request $request, AuthorizationService $authorization)
    {
        $query=IdCardRenewalApplication::with('cadet')->latest();
        if (!$request->user()->hasGlobalAccess()) $query->whereIn('cadet_id',$authorization->cadetQuery($request->user())->select('service_number'));
        return response()->json($query->paginate(20));
    }

    public function store(Request $request, IdCardRenewalService $service)
    {
        $cadet=$request->user()->cadet;
        abort_unless($cadet,403,'This account is not linked to a cadet.');
        $data=$request->validate(['reason'=>['nullable','string','max:2000']]);
        return response()->json($service->apply($cadet,$data['reason']??null),201);
    }

    public function show(Request $request,IdCardRenewalApplication $application,AuthorizationService $authorization)
    {
        $application->loadMissing('cadet');
        abort_unless($authorization->canAccessCadet($request->user(),$application->cadet),403);
        return response()->json($application->load('cadet'));
    }

    public function verifyPayment(Request $request,IdCardRenewalApplication $application,IdCardRenewalService $service){abort_unless($request->user()->hasGlobalAccess(),403);$data=$request->validate(['payment_reference'=>['required','string','max:150']]);return response()->json($service->verifyPayment($application,$data['payment_reference']));}
    public function approve(Request $request,IdCardRenewalApplication $application,IdCardRenewalService $service){abort_unless($request->user()->hasGlobalAccess(),403);return response()->json($service->approve($application,(int)$request->user()->id));}
    public function issue(Request $request,IdCardRenewalApplication $application,IdCardRenewalService $service){abort_unless($request->user()->hasGlobalAccess(),403);return response()->json($service->issue($application));}
}
