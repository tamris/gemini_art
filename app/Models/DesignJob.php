<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DesignJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'designer_id',
        'title',
        'dimensions',
        'material_notes',
        'brief_notes',
        'deadline_at',
    ];

    protected function casts(): array
    {
        return [
            'deadline_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function designer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'designer_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(DesignAsset::class);
    }

    public function finalPrintAssets(): HasMany
    {
        return $this->hasMany(DesignAsset::class)->where('file_type', 'final_print');
    }

    public function previewProofAssets(): HasMany
    {
        return $this->hasMany(DesignAsset::class)->where('file_type', 'preview_proof');
    }

    public function briefRefAssets(): HasMany
    {
        return $this->hasMany(DesignAsset::class)->where('file_type', 'brief_ref');
    }
}
