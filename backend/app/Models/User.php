<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'cadet_id', 'unit_id', 'ward_id', 'lga_id', 'state_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
    }

    public function cadet(): BelongsTo
    {
        return $this->belongsTo(Cadet::class, 'cadet_id', 'service_number');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    public function lga(): BelongsTo
    {
        return $this->belongsTo(Lga::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function hasRole(string $role): bool
    {
        return strtolower(trim((string) $this->role)) === strtolower(trim($role));
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function hasGlobalAccess(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    public function isAdmin(): bool
    {
        return in_array(strtolower((string) $this->role), ['admin', 'administrator'], true);
    }

    public function isNational(): bool
    {
        return in_array(strtolower((string) $this->role), ['national', 'national_admin'], true);
    }

    public function isUnitCommander(): bool
    {
        return in_array(strtolower((string) $this->role), ['unit_commander', 'unit-commander'], true);
    }

    public function isHcs(): bool
    {
        return in_array(strtolower((string) $this->role), ['hcs', 'ward_hcs', 'ward_commander'], true);
    }

    public function isLgaChairman(): bool
    {
        return in_array(strtolower((string) $this->role), ['lga_chairman', 'chairman_self_reliance'], true);
    }

    public function isChairmanSelfReliance(): bool
    {
        return $this->isLgaChairman();
    }

    public function isStateController(): bool
    {
        return in_array(strtolower((string) $this->role), ['state_controller', 'state'], true);
    }

    public function isInstructor(): bool
    {
        return $this->hasRole('instructor');
    }

    public function isCadet(): bool
    {
        return $this->hasRole('cadet');
    }
}
