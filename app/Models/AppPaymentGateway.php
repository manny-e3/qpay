<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AppPaymentGateway extends Pivot
{
    protected $table = 'app_payment_gateways';
    use HasFactory;

    protected $fillable = ['app_config_id', 'payment_gateway_id', 'config', 'is_active', 'callback_url'];

    protected $casts = [
        'config' => 'json',
        'is_active' => 'boolean',
    ];

    public function app()
    {
        return $this->belongsTo(AppConfig::class, 'app_config_id');
    }

    public function gateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_gateway_id');
    }
}
