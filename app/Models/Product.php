<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'sku',
        'purchase_price',
        'sell_price',
        'current_stock',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
    ];

    // Relationships
    public function stockEntries()
    {
        return $this->hasMany(StockEntry::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // Helper methods
    public function addStock(int $quantity, float $purchasePrice, int $addedBy): void
    {
        $this->increment('current_stock', $quantity);
        $this->update(['purchase_price' => $purchasePrice]);

        $this->stockEntries()->create([
            'quantity' => $quantity,
            'purchase_price' => $purchasePrice,
            'added_by' => $addedBy,
        ]);
    }

    public function reduceStock(int $quantity): void
    {
        $this->decrement('current_stock', $quantity);
    }

    public function getStockValue(): float
    {
        return $this->current_stock * $this->purchase_price;
    }
}
