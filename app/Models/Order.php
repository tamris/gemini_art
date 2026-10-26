<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'order_type',
        'total_amount',
        'paid_amount',
        'payment_status',
        'work_status',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function designJob(): HasOne
    {
        return $this->hasOne(DesignJob::class);
    }

    public function cashMovements(): HasMany
    {
        return $this->hasMany(CashMovement::class);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->total_amount - (float) $this->paid_amount);
    }

    public static function generateOrderNumber(Branch $branch): string
    {
        $ym = date('Ym');
        $prefix = "ORD-{$branch->code}-{$ym}-";
        $lastOrder = static::where('order_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastOrder && preg_match('/-(\d+)$/', $lastOrder->order_number, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->work_status) {
            'draft' => 'Draft / Antrean',
            'in_progress' => 'Proses Pengerjaan',
            'revision' => 'Revisi',
            'ready_to_print' => 'Siap Cetak',
            'completed' => 'Selesai / Diambil',
            'canceled' => 'Dibatalkan',
            default => $this->work_status,
        };
    }

    public function getStatusColorClassAttribute(): string
    {
        return match ($this->work_status) {
            'draft' => 'bg-yellow-300 text-black border-black',
            'in_progress' => 'bg-blue-400 text-black border-black',
            'revision' => 'bg-amber-400 text-black border-black',
            'ready_to_print' => 'bg-emerald-400 text-black border-black',
            'completed' => 'bg-slate-200 text-slate-800 border-black',
            'canceled' => 'bg-rose-400 text-black border-black',
            default => 'bg-gray-100 text-black border-black',
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'Lunas',
            'partial_dp' => 'DP (Sebagian)',
            'unpaid' => 'Belum Bayar',
            default => $this->payment_status,
        };
    }

    public function getPaymentStatusBadgeClassAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'bg-emerald-400 text-black',
            'partial_dp' => 'bg-yellow-300 text-black',
            'unpaid' => 'bg-rose-300 text-black',
            default => 'bg-slate-200 text-black',
        };
    }
}
