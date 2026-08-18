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
    public function index(Request $request)
    {
        $query = WardTransfer::with(['cadet', 'fromWard.lga.state', 'toWard.lga.state'])->latest();
        if (!$request->user()->isAdmin()) {
            $query->where('cadet_id', $request->user()->cadet_id);
        }
        return response()->json($query->paginate(20));
    }

    public function store(Request $request, WardTransferService $service)
    {
        $data = $request->validate(['cadet_id' => ['required', 'exists:cadets,id'], 'to_ward_id' => ['required', 'exists:wards,id'], 'reason' => ['nullable', 'string', 'max:2000']]);
        $cadet = Cadet::findOrFail($data['cadet_id']);
        abort_unless($request->user()->isAdmin() || (int) $cadet->id === (int) $request->user()->cadet_id, 403);
        return response()->json($service->apply($cadet, (int) $data['to_ward_id'], $data['reason'] ?? null), 201);
    }

    public function show(Request $request, WardTransfer $transfer)
    {
        abort_unless($request->user()->isAdmin() || (int) $transfer->cadet_id === (int) $request->user()->cadet_id, 403);
        return response()->json($transfer->load(['cadet', 'fromWard.lga.state', 'toWard.lga.state']));
    }

    public function release(Request $request, WardTransfer $transfer, WardTransferService $service)
    {
        abort_unless($request->user()->isAdmin() || ($request->user()->isHcs() && (int) $request->user()->ward_id === (int) $transfer->from_ward_id), 403);
        return response()->json($service->release($transfer, (int) $request->user()->id));
    }

    public function sourceLga(Request $request, WardTransfer $transfer, WardTransferService $service)
    {
        abort_unless($request->user()->isAdmin() || ($request->user()->isLgaChairman() && (int) $request->user()->lga_id === (int) $transfer->fromWard->lga_id), 403);
        return response()->json($service->sourceLga($transfer, (int) $request->user()->id));
    }

    public function sourceState(Request $request, WardTransfer $transfer, WardTransferService $service)
    {
        abort_unless($request->user()->isAdmin() || ($request->user()->isStateController() && (int) $request->user()->state_id === (int) $transfer->fromWard->lga->state_id), 403);
        return response()->json($service->sourceState($transfer, (int) $request->user()->id));
    }

    public function destinationHcs(Request $request, WardTransfer $transfer, WardTransferService $service)
    {
        abort_unless($request->user()->isAdmin() || ($request->user()->isHcs() && (int) $request->user()->ward_id === (int) $transfer->to_ward_id), 403);
        return response()->json($service->destinationHcs($transfer, (int) $request->user()->id));
    }

    public function destinationLga(Request $request, WardTransfer $transfer, WardTransferService $service)
    {
        abort_unless($request->user()->isAdmin() || ($request->user()->isLgaChairman() && (int) $request->user()->lga_id === (int) $transfer->toWard->lga_id), 403);
        return response()->json($service->destinationLga($transfer, (int) $request->user()->id));
    }

    public function destinationState(Request $request, WardTransfer $transfer, WardTransferService $service)
    {
        abort_unless($request->user()->isAdmin() || ($request->user()->isStateController() && (int) $request->user()->state_id === (int) $transfer->toWard->lga->state_id), 403);
        return response()->json($service->destinationState($transfer, (int) $request->user()->id));
    }

    public function nationalApprove(Request $request, WardTransfer $transfer, WardTransferService $service)
    {
        abort_unless($request->user()->isAdmin() || $request->user()->isNational(), 403);
        return response()->json($service->nationalApprove($transfer, (int) $request->user()->id));
    }
}
