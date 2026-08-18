<?php

declare(strict_types=1);

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Cadet;
use App\Models\Unit;
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
        if ($user->isUnitCommander()) $query->where(fn ($q) => $q->where('from_unit_id', $user->unit_id)->orWhere('to_unit_id', $user->unit_id));
        elseif (!$user->isAdmin()) $query->whereHas('cadet', fn ($q) => $q->where('user_id', $user->id));
        $transfers = $query->paginate(15);
        return view('portal.unit-transfers.index', compact('transfers','user'));
    }

    public function create(Request $request): View
    {
        $cadets = $request->user()->isAdmin() ? Cadet::with('unit')->get() : [$request->user()->cadet];
        $units = Unit::orderBy('name')->get();
        return view('portal.unit-transfers.create', compact('cadets','units'));
    }

    public function store(Request $request, UnitTransferService $service): RedirectResponse
    {
        abort_unless($request->user()->isAdmin() || $request->user()->cadet, 403);
        $data = $request->validate(['cadet_id'=>['required','exists:cadets,id'],'to_unit_id'=>['required','exists:units,id'],'reason'=>['nullable','string','max:2000']]);
        if (!$request->user()->isAdmin()) abort_unless((int)$request->user()->cadet->id === (int)$data['cadet_id'], 403);
        $service->apply(Cadet::findOrFail($data['cadet_id']), (int)$data['to_unit_id'], $data['reason'] ?? null);
        return redirect()->route('portal.unit-transfers.index')->with('success','Unit transfer application submitted.');
    }

    public function release(Request $request, UnitTransfer $transfer, UnitTransferService $service): RedirectResponse
    {
        $this->authorize('release', $transfer); $service->release($transfer, (int)$request->user()->id);
        return back()->with('success','Cadet released by the originating unit.');
    }

    public function accept(Request $request, UnitTransfer $transfer, UnitTransferService $service): RedirectResponse
    {
        $this->authorize('accept', $transfer); $service->accept($transfer, (int)$request->user()->id);
        return back()->with('success','Cadet accepted by the destination unit. Payment is now required.');
    }

    public function verifyPayment(Request $request, UnitTransfer $transfer, UnitTransferService $service): RedirectResponse
    {
        $this->authorize('verifyPayment', $transfer);
        $data = $request->validate(['payment_reference'=>['required','string','max:150']]);
        $service->verifyPayment($transfer, $data['payment_reference']);
        return back()->with('success','Payment verified and the cadet has been transferred.');
    }
}
