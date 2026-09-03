@extends('layouts.checkout')

@push('styles')
<style>
    /* Transaction Summary Header */
    .tx-header {
        background: linear-gradient(135deg, #0f1e3d 0%, #1a3668 100%);
        padding: 28px 24px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .tx-header::before {
        content: '';
        position: absolute;
        top: -30px;
        right: -30px;
        width: 120px;
        height: 120px;
        background: rgba(197,147,51,0.12);
        border-radius: 50%;
    }
    .tx-header::after {
        content: '';
        position: absolute;
        bottom: -40px;
        right: 40px;
        width: 80px;
        height: 80px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }
    .paying-to-label {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.5);
        margin-bottom: 4px;
    }
    .app-name {
        font-size: 18px;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 20px;
    }
    .amount-display {
        display: flex;
        align-items: baseline;
        gap: 6px;
        margin-bottom: 16px;
    }
    .currency-badge {
        font-size: 14px;
        font-weight: 700;
        color: var(--gold);
        background: rgba(197,147,51,0.15);
        padding: 3px 8px;
        border-radius: 6px;
    }
    .amount-figure {
        font-size: 36px;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.03em;
        line-height: 1;
    }
    .tx-meta {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
    .tx-meta-item {}
    .tx-meta-label {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: rgba(255,255,255,0.4);
        margin-bottom: 3px;
    }
    .tx-meta-value {
        font-size: 13px;
        font-weight: 600;
        color: rgba(255,255,255,0.85);
    }
    .tx-reference {
        font-family: 'SFMono-Regular', Consolas, monospace;
        font-size: 12px;
        background: rgba(255,255,255,0.08);
        padding: 3px 8px;
        border-radius: 5px;
        color: rgba(255,255,255,0.7);
    }

    /* Gateway Selection Section */
    .gateway-section-header {
        padding: 20px 24px 12px;
    }
    .gateway-section-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }
    .gateway-section-sub {
        font-size: 12px;
        color: var(--text-muted);
    }

    /* Gateway Cards */
    .gateways-list {
        padding: 0 24px 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .gateway-option {
        position: relative;
    }
    .gateway-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    .gateway-label {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px 20px;
        border: 2px solid var(--border);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #ffffff;
        position: relative;
        overflow: hidden;
    }
    .gateway-label:hover {
        border-color: #c5bfe8;
        background: #fafbff;
    }
    .gateway-option input[type="radio"]:checked + .gateway-label {
        border-color: var(--primary);
        background: linear-gradient(135deg, rgba(26,54,104,0.03) 0%, rgba(26,54,104,0.01) 100%);
        box-shadow: 0 2px 12px rgba(26,54,104,0.12);
    }

    /* Active indicator bar */
    .gateway-label::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 0;
        background: var(--primary);
        border-radius: 10px 0 0 10px;
        transition: width 0.2s ease;
    }
    .gateway-option input[type="radio"]:checked + .gateway-label::before {
        width: 4px;
    }

    /* Gateway Icon */
    .gateway-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
        font-weight: 800;
    }
    .gateway-icon-paystack { background: linear-gradient(135deg, #0ba4db15, #0ba4db25); color: #0ba4db; }
    .gateway-icon-flutterwave { background: linear-gradient(135deg, #f5a62315, #f5a62325); color: #f5a623; }
    .gateway-icon-default { background: rgba(26,54,104,0.08); color: var(--primary); }

    .gateway-info { flex: 1; }
    .gateway-name {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 3px;
    }
    .gateway-desc {
        font-size: 12px;
        color: var(--text-muted);
    }

    /* Radio Check UI */
    .gateway-radio-indicator {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }
    .gateway-option input[type="radio"]:checked + .gateway-label .gateway-radio-indicator {
        border-color: var(--primary);
        background: var(--primary);
    }
    .gateway-radio-indicator::after {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: transparent;
        transition: background 0.2s ease;
    }
    .gateway-option input[type="radio"]:checked + .gateway-label .gateway-radio-indicator::after {
        background: #ffffff;
    }

    /* Submit Zone */
    .submit-section {
        padding: 0 24px 24px;
    }
    .submit-section-note {
        font-size: 11px;
        color: var(--text-muted);
        text-align: center;
        margin-top: 10px;
    }

    /* Spinner on submit */
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    .spinner {
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255,255,255,0.3);
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
        display: none;
    }
</style>
@endpush

@section('content')

{{-- Transaction Header --}}
<div class="tx-header">
    <div class="paying-to-label">Paying To</div>
    <div class="app-name">{{ $transaction->app->appName }}</div>

    <div class="amount-display">
        <span class="currency-badge">{{ $transaction->currency }}</span>
        <span class="amount-figure">{{ number_format($transaction->amount, 2) }}</span>
    </div>

    <div class="tx-meta">
        <div class="tx-meta-item">
            <div class="tx-meta-label">Customer</div>
            <div class="tx-meta-value">{{ $transaction->customer_email }}</div>
        </div>
        <div class="tx-meta-item">
            <div class="tx-meta-label">Reference</div>
            <div class="tx-reference">{{ $transaction->reference }}</div>
        </div>
    </div>
</div>

{{-- Gateway Selection --}}
<form action="{{ route('checkout.select', $transaction->reference) }}" method="POST" id="checkout-form">
    @csrf

    <div class="gateway-section-header">
        <div class="gateway-section-title">Select Payment Method</div>
        <div class="gateway-section-sub">Choose how you'd like to complete this payment</div>
    </div>

    <div class="gateways-list">
        @foreach($gateways as $appGateway)
            @php
                $slug = strtolower($appGateway->gateway->slug ?? '');
                $iconClass = 'gateway-icon-default';
                $icon = '💳';
                if (str_contains($slug, 'paystack')) {
                    $iconClass = 'gateway-icon-paystack';
                    $icon = 'P';
                } elseif (str_contains($slug, 'flutterwave')) {
                    $iconClass = 'gateway-icon-flutterwave';
                    $icon = 'F';
                }
            @endphp
            <div class="gateway-option">
                <input 
                    type="radio" 
                    name="gateway_id" 
                    id="gw_{{ $appGateway->gateway->id }}" 
                    value="{{ $appGateway->gateway->id }}"
                    {{ $loop->first ? 'checked' : '' }}
                    required
                >
                <label class="gateway-label" for="gw_{{ $appGateway->gateway->id }}">
                    <div class="gateway-icon-wrap {{ $iconClass }}">{{ $icon }}</div>
                    <div class="gateway-info">
                        <div class="gateway-name">{{ $appGateway->gateway->name }}</div>
                        <div class="gateway-desc">
                            {{ $appGateway->gateway->description ?? 'Secure payment via ' . $appGateway->gateway->name }}
                        </div>
                    </div>
                    <div class="gateway-radio-indicator"></div>
                </label>
            </div>
        @endforeach
    </div>

    <div class="submit-section">
        <button type="submit" class="btn-primary-checkout" id="submit-btn">
            <span id="btn-text">Pay {{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</span>
            <div class="spinner" id="btn-spinner"></div>
        </button>
        <div class="submit-section-note">
            By proceeding, you agree to the payment terms. Your data is encrypted and secure.
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
document.getElementById('checkout-form').addEventListener('submit', function () {
    const btn = document.getElementById('submit-btn');
    const text = document.getElementById('btn-text');
    const spinner = document.getElementById('btn-spinner');
    btn.disabled = true;
    text.textContent = 'Redirecting...';
    spinner.style.display = 'block';
});
</script>
@endpush
