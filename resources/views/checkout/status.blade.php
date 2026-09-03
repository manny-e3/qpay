@extends('layouts.checkout')

@push('styles')
<style>
    .status-body {
        padding: 40px 24px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
    }

    /* Animated Status Icon */
    .status-icon-ring {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        margin-bottom: 4px;
        position: relative;
    }
    .status-icon-ring::before {
        content: '';
        position: absolute;
        inset: -6px;
        border-radius: 50%;
        border: 2px solid transparent;
        animation: ring-pulse 1.6s ease-out infinite;
    }

    /* Success */
    .icon-success { background: var(--success-bg); }
    .icon-success::before { border-color: rgba(26, 124, 74, 0.3); }

    /* Failed */
    .icon-failed { background: var(--error-bg); }
    .icon-failed::before { border-color: rgba(186, 26, 26, 0.3); }

    /* Pending */
    .icon-pending { background: var(--pending-bg); }
    .icon-pending::before { border-color: rgba(123, 84, 0, 0.3); }

    @keyframes ring-pulse {
        0% { transform: scale(1); opacity: 1; }
        100% { transform: scale(1.4); opacity: 0; }
    }

    .status-title {
        font-size: 22px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.02em;
    }
    .status-description {
        font-size: 14px;
        color: var(--text-secondary);
        max-width: 320px;
        line-height: 1.6;
    }

    /* Transaction Summary Pill */
    .tx-summary-pill {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 16px 20px;
        width: 100%;
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .tx-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .tx-row-label {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 500;
    }
    .tx-row-value {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
    }
    .tx-row-value.amount-big {
        font-size: 18px;
        font-weight: 800;
        color: var(--primary);
    }
    .tx-status-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .chip-success { background: var(--success-bg); color: var(--success); }
    .chip-failed { background: var(--error-bg); color: var(--error); }
    .chip-pending { background: var(--pending-bg); color: var(--pending); }
    .chip-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    /* Divider */
    .tx-divider {
        height: 1px;
        background: var(--border);
        margin: 0;
    }

    /* CTA */
    .cta-section {
        width: 100%;
        padding: 0 0 4px;
    }
    .btn-cta-primary {
        display: flex;
        width: 100%;
        padding: 15px 24px;
        background: linear-gradient(135deg, var(--primary), #2a4e8c);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
        margin-bottom: 10px;
    }
    .btn-cta-primary:hover {
        box-shadow: 0 6px 20px rgba(26,54,104,0.25);
        transform: translateY(-1px);
    }
    .btn-cta-secondary {
        display: flex;
        width: 100%;
        padding: 13px 24px;
        background: transparent;
        color: var(--text-secondary);
        border: 1.5px solid var(--border);
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    .btn-cta-secondary:hover {
        border-color: var(--primary);
        color: var(--primary);
    }
</style>
@endpush

@section('content')

@php
    $isSuccess = in_array($transaction->status, ['successful', 'settled', 'success']);
    $isFailed  = in_array($transaction->status, ['failed', 'reversed', 'cancelled']);
    $isPending = !$isSuccess && !$isFailed;

    $icon = $isSuccess ? '✓' : ($isFailed ? '✕' : '⏳');
    $iconClass = $isSuccess ? 'icon-success' : ($isFailed ? 'icon-failed' : 'icon-pending');
    $title = $isSuccess ? 'Payment Successful!' : ($isFailed ? 'Payment Failed' : 'Processing...');
    $description = $isSuccess
        ? 'Your payment has been confirmed and securely processed by the FMDQ Payment Hub.'
        : ($isFailed
            ? 'We were unable to complete this transaction. Please try again or use a different payment method.'
            : 'Your payment is still being processed. Please wait or check back shortly.');

    $chipClass = $isSuccess ? 'chip-success' : ($isFailed ? 'chip-failed' : 'chip-pending');
@endphp

<div class="status-body">

    <!-- Status Icon -->
    <div class="status-icon-ring {{ $iconClass }}">
        <span>{{ $icon }}</span>
    </div>

    <!-- Title & Description -->
    <div class="status-title">{{ $title }}</div>
    <div class="status-description">{{ $description }}</div>

    <!-- Transaction Details -->
    <div class="tx-summary-pill">
        <div class="tx-row">
            <span class="tx-row-label">Merchant</span>
            <span class="tx-row-value">{{ $transaction->app->appName ?? '—' }}</span>
        </div>
        <div class="tx-divider"></div>
        <div class="tx-row">
            <span class="tx-row-label">Amount Paid</span>
            <span class="tx-row-value amount-big">{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</span>
        </div>
        <div class="tx-divider"></div>
        <div class="tx-row">
            <span class="tx-row-label">Reference</span>
            <span class="tx-row-value" style="font-family: monospace; font-size: 12px;">{{ $transaction->reference }}</span>
        </div>
        <div class="tx-divider"></div>
        <div class="tx-row">
            <span class="tx-row-label">Status</span>
            <span class="tx-status-chip {{ $chipClass }}">
                <span class="chip-dot"></span>
                {{ ucfirst($transaction->status) }}
            </span>
        </div>
        @if($transaction->gateway)
        <div class="tx-divider"></div>
        <div class="tx-row">
            <span class="tx-row-label">Processed via</span>
            <span class="tx-row-value">{{ $transaction->gateway->name }}</span>
        </div>
        @endif
    </div>

    <!-- CTA -->
    <div class="cta-section">
        @if($transaction->callback_url)
            @php
                $sep = parse_url($transaction->callback_url, PHP_URL_QUERY) ? '&' : '?';
                $returnUrl = $transaction->callback_url . $sep . "status={$transaction->status}&reference={$transaction->reference}";
            @endphp
            <a href="{{ $returnUrl }}" class="btn-cta-primary">
                Return to {{ $transaction->app->appName ?? 'Merchant' }}
            </a>
        @endif

        @if($isFailed)
            <a href="{{ route('checkout.index', $transaction->reference) }}" class="btn-cta-secondary">
                Try Again
            </a>
        @elseif($isPending)
            <a href="{{ url()->current() }}" class="btn-cta-secondary" onclick="location.reload(); return false;">
                ↻ &nbsp;Refresh Status
            </a>
        @endif
    </div>

</div>

@endsection
