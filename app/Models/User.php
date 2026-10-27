<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'branch_id',
        'name',
        'email',
        'phone',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function designJobs(): HasMany
    {
        return $this->hasMany(DesignJob::class, 'designer_id');
    }

    public function cashMovements(): HasMany
    {
        return $this->hasMany(CashMovement::class);
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }

    public function isDesigner(): bool
    {
        return $this->role === 'designer';
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'owner' => 'Owner',
            'cashier' => 'Kasir',
            'designer' => 'Desainer',
            default => ucfirst($this->role),
        };
    }

    public function getRoleBadgeClassAttribute(): string
    {
        return match ($this->role) {
            'owner' => 'bg-purple-100 text-purple-900 border-purple-300',
            'cashier' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
            'designer' => 'bg-sky-100 text-sky-900 border-sky-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }

    public function getAvatarColorClassAttribute(): string
    {
        return match ($this->role) {
            'owner' => 'bg-purple-200 text-purple-950 border-purple-800',
            'cashier' => 'bg-emerald-200 text-emerald-950 border-emerald-800',
            'designer' => 'bg-blue-200 text-blue-950 border-blue-800',
            default => 'bg-amber-200 text-amber-950 border-amber-800',
        };
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= strtoupper(substr($w, 0, 1));
        }
        return $initials ?: 'ST';
    }
}
