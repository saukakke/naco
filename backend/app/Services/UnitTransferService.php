<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cadet;
use App\Models\UnitTransfer;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UnitTransferService
{
    public function apply(Cadet $cadet, int $toUnitId, ?string $reason = null): UnitTransfer
    {
        if ((int) $cadet->unit_id === $toUnitId) {
            throw ValidationException::withMessages(['to_unit_id' => 'The destination unit must be different from the current unit.']);
        }

        if (UnitTransfer::where('cadet_id', $cadet->id)->whereIn('status', ['pending_release', 'released', 'pending_acceptance', 'accepted', 'payment_pending', 'payment_verification'])->exists()) {
            throw ValidationException::withMessages(['cadet' => 'The cadet already has an active unit transfer.']);
        }

        return UnitTransfer::create([
            'cadet_id' => $cadet->id,
            'from_unit_id' => $cadet->unit_id,
            'to_unit_id' => $toUnitId,
            'reason' => $reason,
            'status' => 'pending_release',
            'applied_at' => now(),
        ]);
    }

    public function release(UnitTransfer $transfer, int $commanderId): UnitTransfer
    {
        if ($transfer->status !== 'pending_release') {
            throw ValidationException::withMessages(['transfer' => 'This transfer is not awaiting release.']);
        }

        $transfer->update(['status' => 'released', 'released_by' => $commanderId, 'released_at' => now()]);
        return $transfer->fresh();
    }

    public function accept(UnitTransfer $transfer, int $commanderId): UnitTransfer
    {
        if ($transfer->status !== 'released') {
            throw ValidationException::withMessages(['transfer' => 'The originating unit must release the cadet first.']);
        }

        $transfer->update(['status' => 'accepted', 'accepted_by' => $commanderId, 'accepted_at' => now()]);
        return $transfer->fresh();
    }

    public function verifyPayment(UnitTransfer $transfer, string $paymentReference): UnitTransfer
    {
        if ($transfer->status !== 'accepted') {
            throw ValidationException::withMessages(['transfer' => 'Both unit commanders must approve the transfer before payment verification.']);
        }

        return DB::transaction(function () use ($transfer, $paymentReference): UnitTransfer {
            $transfer->update([
                'status' => 'completed',
                'payment_reference' => $paymentReference,
                'payment_verified_at' => now(),
                'completed_at' => now(),
            ]);

            $transfer->cadet()->update(['unit_id' => $transfer->to_unit_id]);

            return $transfer->fresh();
        });
    }
}
