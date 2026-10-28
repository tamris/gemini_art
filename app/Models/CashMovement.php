<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'order_id',
        'user_id',
        'type',
        'amount',
        'payment_method',
        'category',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'order_dp' => 'DP Order Desain',
            'order_settlement' => 'Pelunasan Order',
            'retail_pos' => 'Kasir Ritel / POS',
            'petty_cash_expense' => 'Pengeluaran Operasional',
            'initial_balance' => 'Modal Kas Awal',
            'order_refund' => 'Pengembalian Dana (Refund DP)',
            default => $this->category,
        };
    }

    public function getCategoryBadgeClassAttribute(): string
    {
        return match ($this->category) {
            'order_dp' => 'bg-yellow-300 text-black',
            'order_settlement' => 'bg-emerald-400 text-black',
            'retail_pos' => 'bg-cyan-300 text-black',
            'petty_cash_expense' => 'bg-rose-400 text-black',
            'initial_balance' => 'bg-purple-300 text-black',
            'order_refund' => 'bg-rose-500 text-white',
            default => 'bg-slate-200 text-black',
        };
    }
}
