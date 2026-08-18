<?php

declare(strict_types=1);

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Cadet;
use App\Models\UnitTransfer;
use App\Services\UnitTransferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitTransferController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = UnitTransfer::with(['cadet','fromUnit','toUnit'])->latest();
        $transfers = $query->paginate(15);
        return view('portal.unit-transfers.index', compact('transfers','user'));
    }

    public function create(Request $request): View
    {
        $cadets = $request->user()->cadet ? [$request->user()->cadet] : Cadet::with('unit')->get();
        $units = \App\Models\Unit::orderBy('name')->get();
        return view('portal.unit-transfers.create', compact('cadets','units'));
    }

    public function store(Request $request, UnitTransferService $service): RedirectResponse
    {
        $data = $request->validate([
            'cadet_id' => ['required','exists:cadets,id'],
            'to_unit_id' => ['required','exists:units,id'],
            'reason' => ['nullable','string','max:2000'],
        ]);
        $service->apply(Cadet::findOrFail($data['cadet_id']), (int) $data['to_unit_id'], $data['reason'] ?? null);
        return redirect()->route('portal.unit-transfers.index')->with('success','Unit transfer application submitted.');
    }

    public function release(Request $request, UnitTransfer $transfer, UnitTransferService $service): RedirectResponse
    {
        $service->release($transfer, (int) $request->user()->id);
        return back()->with('success','Cadet released by the originating unit.');
    }

    public function accept(Request $request, UnitTransfer $transfer, UnitTransferService $service): RedirectResponse
    {
        $service->accept($transfer, (int) $request->user()->id);
        return back()->with('success','Cadet accepted by the destination unit.');
    }
}
