<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\UnitTransfer;
use App\Models\User;
use App\Services\AuthorizationService;

class UnitTransferPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, UnitTransfer $transfer): bool
    {
        $transfer->loadMissing('cadet');
        return app(AuthorizationService::class)->canAccessCadet($user, $transfer->cadet);
    }
    public function release(User $user, UnitTransfer $transfer): bool { return $user->isAdmin() || ($user->isUnitCommander() && $user->unit_id === $transfer->from_unit_id); }
    public function accept(User $user, UnitTransfer $transfer): bool { return $user->isAdmin() || ($user->isUnitCommander() && $user->unit_id === $transfer->to_unit_id); }
    public function verifyPayment(User $user, UnitTransfer $transfer): bool { return $user->isAdmin(); }
}
