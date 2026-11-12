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
        'design_fee',
        'dimensions',
        'material_notes',
        'brief_notes',
        'revision_notes',
        'revision_count',
        'deadline_at',
        'vendor_email_sent_at',
        'vendor_email_sent_to',
        'vendor_email_sent_by',
        'vendor_email_sent_count',
        'vendor_email_sent_asset_ids',
    ];

    protected function casts(): array
    {
        return [
            'design_fee' => 'decimal:2',
            'deadline_at' => 'datetime',
            'revision_count' => 'integer',
            'vendor_email_sent_at' => 'datetime',
            'vendor_email_sent_count' => 'integer',
            'vendor_email_sent_asset_ids' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (DesignJob $job) {
            foreach ($job->assets as $asset) {
                $asset->delete();
            }
        });
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

    public function emailSender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_email_sent_by');
    }

    /**
     * Persist verified vendor email dispatch audit metadata after Mail::send succeeds.
     *
     * @param  array<int, int|string>  $assetIds
     */
    public function recordVendorEmailDispatch(string $recipientEmail, ?int $senderUserId, array $assetIds): void
    {
        $normalizedIds = array_values(array_unique(array_map('intval', $assetIds)));
        sort($normalizedIds);

        $this->forceFill([
            'vendor_email_sent_at' => now(),
            'vendor_email_sent_to' => $recipientEmail,
            'vendor_email_sent_by' => $senderUserId,
            'vendor_email_sent_count' => (int) ($this->vendor_email_sent_count ?? 0) + 1,
            'vendor_email_sent_asset_ids' => $normalizedIds,
        ])->save();
    }

    /**
     * Resolve UI badge state for vendor email dispatch using Option A (strict file & revision diff).
     *
     * @return array<string, mixed>
     */
    public function resolveEmailDispatchState(?Order $order = null): array
    {
        $order = $order ?? $this->order;

        $finalAssets = match (true) {
            $this->relationLoaded('finalPrintAssets') => $this->finalPrintAssets,
            $this->relationLoaded('assets') => $this->assets->where('file_type', 'final_print')->values(),
            default => $this->finalPrintAssets()->get(['id', 'design_job_id', 'file_type', 'created_at']),
        };

        $currentIds = $finalAssets->pluck('id')->map(fn ($id) => (int) $id)->sort()->values()->all();
        $hasFinalPrint = count($currentIds) > 0;
        $sentAt = $this->vendor_email_sent_at;
        $senderName = $this->vendor_email_sent_by ? $this->emailSender?->name : null;

        $base = [
            'status' => 'none',
            'label' => null,
            'reason' => null,
            'detail' => null,
            'sent_at' => $sentAt,
            'sent_to' => $this->vendor_email_sent_to,
            'sent_by' => $senderName,
            'sent_count' => (int) ($this->vendor_email_sent_count ?? 0),
            'final_count' => count($currentIds),
        ];

        if ($order?->isCanceled()) {
            return $base;
        }

        if ($sentAt === null) {
            if (!$hasFinalPrint || $order?->isCompleted()) {
                return $base;
            }

            return array_merge($base, [
                'status' => 'pending',
                'label' => 'Belum Kirim Email',
                'detail' => 'File cetak final sudah siap, belum dikirim ke percetakan via email.',
            ]);
        }

        if ($order?->isCompleted()) {
            return array_merge($base, [
                'status' => 'sent',
                'label' => 'Email Terkirim',
                'detail' => 'Berkas cetak telah dikirim ke percetakan via email.',
            ]);
        }

        $sentIds = $this->vendor_email_sent_asset_ids;
        $hasAddedFile = false;
        $hasRemovedFile = false;

        if (is_array($sentIds)) {
            $normalizedSentIds = array_values(array_unique(array_map('intval', $sentIds)));
            sort($normalizedSentIds);

            $hasAddedFile = !empty(array_diff($currentIds, $normalizedSentIds));
            $hasRemovedFile = !empty(array_diff($normalizedSentIds, $currentIds));
        } elseif ($finalAssets->contains(fn ($asset) => $asset->created_at && $asset->created_at->gt($sentAt))) {
            $hasAddedFile = true;
        }

        if ($hasAddedFile) {
            return array_merge($base, [
                'status' => 'needs_resend',
                'reason' => 'new_file',
                'label' => 'File Baru · Kirim Ulang',
                'detail' => 'Ada berkas cetak final baru setelah email terakhir dikirim.',
            ]);
        }

        if ($hasRemovedFile) {
            return array_merge($base, [
                'status' => 'needs_resend',
                'reason' => 'file_deleted',
                'label' => 'File Dihapus · Kirim Ulang',
                'detail' => 'Berkas cetak final diubah atau dihapus setelah email terakhir dikirim.',
            ]);
        }

        if ($order?->work_status === 'revision') {
            return array_merge($base, [
                'status' => 'needs_resend',
                'reason' => 'revision',
                'label' => 'Revisi · Kirim Ulang',
                'detail' => 'Order masuk status revisi setelah email terakhir dikirim.',
            ]);
        }

        return array_merge($base, [
            'status' => 'sent',
            'label' => 'Email Terkirim',
            'detail' => 'Berkas cetak final terkini sudah terkirim via email.',
        ]);
    }

    /**
     * Return physical print dimensions without billing minimum-area annotations.
     */
    public function getCleanDimensionsAttribute(): ?string
    {
        if ($this->dimensions === null) {
            return null;
        }

        $cleaned = preg_replace('/\s*[,·]?\s*Min\.\s*1\s*m²/ui', '', $this->dimensions);

        return trim((string) preg_replace('/\s{2,}/', ' ', (string) $cleaned));
    }
}
