<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermanentOrder extends Model
{
    protected $fillable = [
        'business_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'product_id',
        'quantity',
        'unit_price',
        'total_amount',
        'paid_amount',
        'due_amount',
        'voucher_number',
        'status',
        'order_date',
        'delivery_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'order_date' => 'date',
        'delivery_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->voucher_number)) {
                $order->voucher_number = 'PO-' . strtoupper(uniqid());
            }
            $order->due_amount = $order->total_amount - $order->paid_amount;
            
            // Set initial status based on payment
            if (!isset($order->status)) {
                if ($order->paid_amount >= $order->total_amount) {
                    $order->status = 'completed';
                } elseif ($order->paid_amount > 0) {
                    $order->status = 'partial';
                } else {
                    $order->status = 'active';
                }
            }
        });

        static::updating(function ($order) {
            // Always recalculate due amount
            if ($order->isDirty(['total_amount', 'paid_amount'])) {
                $order->due_amount = $order->total_amount - $order->paid_amount;
            }
            
            // Auto-update status only if not manually set
            if (!$order->isDirty('status') && $order->isDirty(['paid_amount', 'total_amount'])) {
                if ($order->paid_amount >= $order->total_amount) {
                    $order->status = 'completed';
                } elseif ($order->paid_amount > 0) {
                    $order->status = 'partial';
                } else {
                    $order->status = 'active';
                }
            }
        });
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isPaid(): bool
    {
        return $this->paid_amount >= $this->total_amount;
    }

    public function isPartiallyPaid(): bool
    {
        return $this->paid_amount > 0 && $this->paid_amount < $this->total_amount;
    }

    public function getDuePercentageAttribute(): float
    {
        if ($this->total_amount == 0) return 0;
        return ($this->due_amount / $this->total_amount) * 100;
    }
}

