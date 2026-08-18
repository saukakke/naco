<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cadet;
use App\Models\UnitTransfer;
use App\Services\UnitTransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UnitTransferController extends Controller
{
    public function store(Request $request, Cadet $cadet, UnitTransferService $service): JsonResponse
    {
        abort_unless($request->user()->isAdmin() || (int) $request->user()->cadet_id === (int) $cadet->id, 403);
        $data = $request->validate(['to_unit_id' => ['required', 'exists:units,id'], 'reason' => ['nullable', 'string', 'max:2000']]);
        return response()->json($service->apply($cadet, (int) $data['to_unit_id'], $data['reason'] ?? null), 201);
    }

    public function release(Request $request, UnitTransfer $transfer, UnitTransferService $service): JsonResponse
    {
        abort_unless($request->user()->isAdmin() || ($request->user()->isUnitCommander() && (int) $request->user()->unit_id === (int) $transfer->from_unit_id), 403);
        return response()->json($service->release($transfer, (int) $request->user()->id));
    }

    public function accept(Request $request, UnitTransfer $transfer, UnitTransferService $service): JsonResponse
    {
        abort_unless($request->user()->isAdmin() || ($request->user()->isUnitCommander() && (int) $request->user()->unit_id === (int) $transfer->to_unit_id), 403);
        return response()->json($service->accept($transfer, (int) $request->user()->id));
    }

    public function verifyPayment(Request $request, UnitTransfer $transfer, UnitTransferService $service): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        $data = $request->validate(['payment_reference' => ['required', 'string', 'max:150']]);
        return response()->json($service->verifyPayment($transfer, $data['payment_reference']));
    }

    public function show(Request $request, UnitTransfer $transfer): JsonResponse
    {
        abort_unless($request->user()->isAdmin() || (int) $request->user()->cadet_id === (int) $transfer->cadet_id, 403);
        return response()->json($transfer->load(['cadet', 'fromUnit', 'toUnit']));
    }
}
