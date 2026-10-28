<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DesignAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'design_job_id',
        'uploaded_by',
        'file_type',
        'file_path',
        'disk',
        'file_name',
        'file_size',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function designJob(): BelongsTo
    {
        return $this->belongsTo(DesignJob::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getFileUrlAttribute(): string
    {
        $disk = $this->disk ?: config('filesystems.archive_disk', 'public');
        try {
            return Storage::disk($disk)->url($this->file_path);
        } catch (\Throwable $e) {
            return Storage::disk('public')->url($this->file_path);
        }
    }

    public function getIsImageAttribute(): bool
    {
        $ext = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']);
    }

    public function getFileTypeBadgeAttribute(): string
    {
        return match ($this->file_type) {
            'final_print' => 'Cetak Final',
            'preview_proof' => 'Preview / Proofing',
            'brief_ref' => 'Referensi Brief',
            default => $this->file_type,
        };
    }
}
