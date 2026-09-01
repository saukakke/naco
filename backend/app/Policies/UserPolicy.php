<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function manageAdmins(User $actor): bool
    {
        return $actor->isSuperAdmin();
    }

    public function manage(User $actor, User $target): bool
    {
        if ($actor->isSuperAdmin()) return true;
        return $actor->isAdmin() && !$target->isSuperAdmin();
    }

    public function view(User $actor, User $target): bool
    {
        if ($actor->hasGlobalAccess() || $actor->isNational()) return true;
        if ($actor->id === $target->id) return true;
        if ($actor->isInstructor()) {
            return $actor->cadet_id !== null && $target->cadet_id === $actor->cadet_id;
        }
        if ($actor->isCadet()) return $actor->cadet_id !== null && $target->cadet_id === $actor->cadet_id;
        return false;
    }
}
