<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\WardTransfer;
use App\Services\AuthorizationService;

class WardTransferPolicy
{
    public function viewAny(User $user): bool { return true; }

    public function view(User $user, WardTransfer $transfer): bool
    {
        return app(AuthorizationService::class)->canAccessWardTransfer($user, $transfer);
    }

    public function release(User $user, WardTransfer $transfer): bool
    {
        if ($user->hasGlobalAccess() || $user->isNational()) return true;
        if ($user->isHcs()) return (int) $user->ward_id === (int) $transfer->from_ward_id;
        return false;
    }

    public function accept(User $user, WardTransfer $transfer): bool
    {
        if ($user->hasGlobalAccess() || $user->isNational()) return true;
        if ($user->isHcs()) return (int) $user->ward_id === (int) $transfer->to_ward_id;
        if ($user->isLgaChairman()) return in_array((int) $user->lga_id, [$transfer->fromWard?->lga_id, $transfer->toWard?->lga_id], true);
        return false;
    }

    public function approve(User $user, WardTransfer $transfer): bool
    {
        if ($user->hasGlobalAccess() || $user->isNational()) return true;
        if ($user->isStateController()) return in_array((int) $user->state_id, [$transfer->fromWard?->lga?->state_id, $transfer->toWard?->lga?->state_id], true);
        return false;
    }
}
