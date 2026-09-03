<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppConfig extends Model
{
    use HasFactory;
    protected $table = 'app_config';
    protected $guarded = [];

    protected $casts = [
        'otp_configured' => 'boolean',
    ];

    public function gateways()
    {
        return $this->belongsToMany(PaymentGateway::class, 'app_payment_gateways', 'app_config_id', 'payment_gateway_id')
            ->using(AppPaymentGateway::class)
            ->withPivot('config', 'is_active', 'callback_url')
            ->withTimestamps();
    }

    /**
     * Retrieve the model for a bound value.
     * Overridden to lazily persist external API applications.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $app = parent::resolveRouteBinding($value, $field);
        if ($app) {
            return $app;
        }

        return \App\Services\AuthService::getAndPersistApp($value);
    }
}
