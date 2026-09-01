<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cadet;
use App\Models\User;
use App\Models\Ward;
use App\Models\WardTransfer;
use Illuminate\Database\Eloquent\Builder;

class AuthorizationService
{
    public function canAccessCadet(User $user, Cadet $cadet): bool
    {
        if ($user->hasGlobalAccess() || $user->isNational()) return true;
        if ($user->isCadet() && $user->cadet_id === $cadet->service_number) return true;
        if ($user->isUnitCommander()) return $user->unit_id !== null && (int) $user->unit_id === (int) $cadet->unit_id;

        $ward = $cadet->ward;
        if (!$ward) return false;
        if ($user->isHcs()) return $user->ward_id !== null && (int) $user->ward_id === (int) $ward->id;
        if ($user->isLgaChairman()) return $user->lga_id !== null && (int) $user->lga_id === (int) $ward->lga_id;
        if ($user->isStateController()) return $user->state_id !== null && (int) $user->state_id === (int) $ward->lga?->state_id;
        return false;
    }

    public function canManageCadet(User $user, Cadet $cadet): bool
    {
        if ($user->hasGlobalAccess() || $user->isNational()) return true;
        if ($user->isCadet() || $user->isInstructor()) return false;
        return $this->canAccessCadet($user, $cadet);
    }

    public function cadetQuery(User $user): Builder
    {
        $query = Cadet::query();
        if ($user->hasGlobalAccess() || $user->isNational()) return $query;
        if ($user->isUnitCommander() && $user->unit_id !== null) return $query->where('unit_id', $user->unit_id);
        if ($user->isHcs() && $user->ward_id !== null) return $query->where('ward_id', $user->ward_id);
        if ($user->isLgaChairman() && $user->lga_id !== null) return $query->whereHas('ward', fn (Builder $q) => $q->where('lga_id', $user->lga_id));
        if ($user->isStateController() && $user->state_id !== null) return $query->whereHas('ward.lga', fn (Builder $q) => $q->where('state_id', $user->state_id));
        if ($user->isCadet() && $user->cadet_id !== null) return $query->where('service_number', $user->cadet_id);
        return $query->whereRaw('1 = 0');
    }

    public function canAccessWard(User $user, int $wardId): bool
    {
        if ($user->hasGlobalAccess() || $user->isNational()) return true;
        if ($user->isHcs()) return $user->ward_id !== null && (int) $user->ward_id === $wardId;
        if ($user->isLgaChairman()) return $user->lga_id !== null && Ward::whereKey($wardId)->where('lga_id', $user->lga_id)->exists();
        if ($user->isStateController()) return $user->state_id !== null && Ward::whereKey($wardId)->whereHas('lga', fn (Builder $q) => $q->where('state_id', $user->state_id))->exists();
        return false;
    }

    public function canAccessWardTransfer(User $user, WardTransfer $transfer): bool
    {
        if ($user->hasGlobalAccess() || $user->isNational()) return true;
        if ($user->isCadet() && $user->cadet_id === $transfer->cadet_id) return true;
        $transfer->loadMissing('fromWard.lga', 'toWard.lga');
        if ($user->isHcs()) return $user->ward_id !== null && in_array((int) $user->ward_id, [(int) $transfer->from_ward_id, (int) $transfer->to_ward_id], true);
        if ($user->isLgaChairman()) return $user->lga_id !== null && in_array((int) $user->lga_id, [(int) $transfer->fromWard->lga_id, (int) $transfer->toWard->lga_id], true);
        if ($user->isStateController()) return $user->state_id !== null && in_array((int) $user->state_id, [(int) $transfer->fromWard->lga->state_id, (int) $transfer->toWard->lga->state_id], true);
        return false;
    }

    public function wardTransferQuery(User $user): Builder
    {
        $query = WardTransfer::query();
        if ($user->hasGlobalAccess() || $user->isNational()) return $query;
        if ($user->isCadet() && $user->cadet_id !== null) return $query->where('cadet_id', $user->cadet_id);
        if ($user->isHcs() && $user->ward_id !== null) return $query->where(fn (Builder $q) => $q->where('from_ward_id', $user->ward_id)->orWhere('to_ward_id', $user->ward_id));
        if ($user->isLgaChairman() && $user->lga_id !== null) return $query->where(fn (Builder $q) => $q->whereHas('fromWard', fn (Builder $w) => $w->where('lga_id', $user->lga_id))->orWhereHas('toWard', fn (Builder $w) => $w->where('lga_id', $user->lga_id)));
        if ($user->isStateController() && $user->state_id !== null) return $query->where(fn (Builder $q) => $q->whereHas('fromWard.lga', fn (Builder $l) => $l->where('state_id', $user->state_id))->orWhereHas('toWard.lga', fn (Builder $l) => $l->where('state_id', $user->state_id)));
        return $query->whereRaw('1 = 0');
    }
}
