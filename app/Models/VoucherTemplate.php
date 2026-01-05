<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'company_name',
        'company_address',
        'company_phone',
        'header_text',
        'footer_text',
        'primary_color',
        'secondary_color',
        'font_size',
        'page_margin',
        'logo_url',
        'show_watermark',
        'watermark_text',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
}
