<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopSetting extends Model
{
    protected $fillable = [
        'owner_id',
        'shop_name',
        'shop_logo',
        'primary_color',
        'secondary_color',
        'accent_color',
        'text_color',
        'font_family',
        'custom_css',
        'company_address',
        'company_phone',
        'company_email',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
