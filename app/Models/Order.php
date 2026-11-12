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
        'total_cost',
        'paid_amount',
        'payment_status',
        'work_status',
        'cancellation_reason',
        'cancellation_type',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (Order $order) {
            if ($order->designJob) {
                $order->designJob->delete();
            }
        });
    }

    public function isCompleted(): bool
    {
        return $this->work_status === 'completed';
    }

    public function isCanceled(): bool
    {
        return $this->work_status === 'canceled';
    }

    public function isLocked(): bool
    {
        return in_array($this->work_status, ['completed', 'canceled']);
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

    public function getGrossProfitAttribute(): ?float
    {
        if ($this->total_cost === null) {
            return null;
        }

        return round((float) $this->total_amount - (float) $this->total_cost, 2);
    }

    public function getGrossProfitMarginAttribute(): ?float
    {
        if ($this->total_cost === null || (float) $this->total_amount <= 0) {
            return null;
        }

        return round(((float) $this->gross_profit / (float) $this->total_amount) * 100, 1);
    }

    public function getIsCostCompleteAttribute(): bool
    {
        if ($this->total_cost === null) {
            return false;
        }

        // If items relation is loaded or exists, check if all items have cost_price
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();
        if ($items->isEmpty()) {
            return false;
        }

        return $items->every(fn($item) => $item->cost_price !== null);
    }

    public function recalculateTotalCost(): void
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();

        if ($items->isEmpty()) {
            $this->total_cost = null;
            $this->save();
            return;
        }

        $allFilled = $items->every(fn($item) => $item->cost_price !== null);
        if ($allFilled) {
            $this->total_cost = $items->sum(fn($item) => ((int) $item->qty) * ((float) $item->cost_price));
        } else {
            $this->total_cost = null;
        }

        $this->save();
    }

    public static function generateOrderNumber(Branch $branch, string $orderType = 'custom_design'): string
    {
        $ym = date('ym'); // Format tahun & bulan 2 digit, e.g. 2611

        $prefix = $orderType === 'retail_pos' 
            ? "POS-{$branch->code}-{$ym}-" 
            : "{$branch->code}-{$ym}-";

        // Query order terakhir khusus di cabang dan bulan berjalan
        $lastOrder = static::where('order_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        // Jika bulan berganti / belum ada order di bulan ini, otomatis reset mulai dari 1 (001)
        $sequence = 1;
        if ($lastOrder && preg_match('/-(\d+)$/', $lastOrder->order_number, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return $prefix . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->work_status) {
            'draft' => 'Draft',
            'in_progress' => 'Proses',
            'revision' => 'Revisi',
            'ready_to_print' => 'Siap Cetak',
            'completed' => 'Selesai',
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

    /**
     * Resolve vendor email dispatch status for design/printing orders.
     */
    public function getEmailDispatchStateAttribute(): ?array
    {
        if ($this->order_type !== 'design_printing' || !$this->designJob) {
            return null;
        }

        return $this->designJob->resolveEmailDispatchState($this);
    }
}
