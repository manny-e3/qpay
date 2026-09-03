<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_config_id',
        'payment_gateway_id',
        'reference',
        'amount',
        'currency',
        'status',
        'customer_email',
        'customer_first_name',
        'customer_last_name',
        'customer_phone',
        'customer_company',
        'callback_url',
        'metadata',
        'gateway_response',
        'invoice_url',
        'receipt_url'
    ];

    protected $casts = [
        'metadata' => 'json',
        'gateway_response' => 'json',
    ];

    public function app()
    {
        return $this->belongsTo(AppConfig::class, 'app_config_id');
    }

    /**
     * Resolve the app relation dynamically in-memory if it does not exist in the database.
     *
     * @return \App\Models\AppConfig|null
     */
    public function getAppAttribute()
    {
        if (!$this->relationLoaded('app') || !$this->getRelation('app')) {
            $app = \App\Services\AuthService::getAndPersistApp($this->app_config_id);
            $this->setRelation('app', $app);
        }
        return $this->getRelation('app');
    }

    public function gateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_gateway_id');
    }
}
