<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'is_active',
        'enable_permanent_orders',
        'enable_credit_system',
        'credit_limit',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'enable_permanent_orders' => 'boolean',
        'enable_credit_system' => 'boolean',
        'credit_limit' => 'decimal:2',
    ];

    public function owners(): HasMany
    {
        return $this->hasMany(User::class, 'business_id')->role('owner');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'business_id');
    }

    public function voucherTemplate()
    {
        return $this->hasOne(VoucherTemplate::class, 'business_id');
    }

    public function permanentOrders(): HasMany
    {
        return $this->hasMany(PermanentOrder::class, 'business_id');
    }
}
