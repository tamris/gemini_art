<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'item_name',
        'qty',
        'unit',
        'unit_price',
        'cost_price',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'unit_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getTotalCostAttribute(): ?float
    {
        if ($this->cost_price === null) {
            return null;
        }

        return round(((float) $this->cost_price) * ((int) $this->qty), 2);
    }

    public function getProfitAttribute(): ?float
    {
        if ($this->total_cost === null) {
            return null;
        }

        return round(((float) $this->subtotal) - ((float) $this->total_cost), 2);
    }
}
