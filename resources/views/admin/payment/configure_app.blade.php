@extends('layouts.admin')

@section('title', 'Configure Payment | ' . $app->appName)
@section('page_title', 'Payment Configuration: ' . $app->appName)
@section('page_subtitle', 'Setup and manage payment gateways for this application.')

@section('content')
<div class="nk-block">
    <form action="{{ route('admin.apps.payment.save', $app->id) }}" method="POST">
        @csrf
        <div class="card card-bordered">
            <div class="card-inner">
                <div class="preview-block">
                    <span class="preview-title-lg overline-title mb-4">Available Gateways</span>
                    
                    <div class="row g-4">
                        @foreach($gateways as $gateway)
                        <div class="col-md-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="custom-control custom-checkbox custom-control-lg mb-3">
                                        <input type="checkbox" 
                                               id="is_active_{{ $gateway->id }}" 
                                               name="gateways[{{ $gateway->id }}][is_active]" 
                                               value="1" 
                                               class="custom-control-input" 
                                               {{ isset($appGateways[$gateway->id]) && $appGateways[$gateway->id]->is_active ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active_{{ $gateway->id }}">
                                            <span class="h6 text-primary">{{ $gateway->name }}</span>
                                        </label>
                                    </div>
                                    <p class="text-soft">Enable transactions via {{ $gateway->name }} for this application.</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <hr class="preview-hr">
                    
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="payment_callback_url">Payment Callback URL</label>
                                <div class="form-control-wrap">
                                    @php
                                        // Fetch existing callback URL from first active gateway, or fallback to first configured gateway
                                        $existingCallback = '';
                                        foreach($appGateways as $ag) {
                                            if ($ag->is_active && $ag->callback_url) {
                                                $existingCallback = $ag->callback_url;
                                                break;
                                            }
                                        }
                                        if (!$existingCallback && count($appGateways) > 0) {
                                            $existingCallback = $appGateways->first()->callback_url;
                                        }
                                    @endphp
                                    <input type="url" 
                                           id="payment_callback_url" 
                                           name="payment_callback_url" 
                                           class="form-control form-control-lg" 
                                           value="{{ $existingCallback }}" 
                                           placeholder="https://example.com/payment/callback">
                                </div>
                                <span class="form-note">The URL redirected to after a transaction is completed. This callback URL will apply to all enabled payment gateways.</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row gy-4 mt-4">
                        <div class="col-12">
                            <ul class="align-center flex-wrap g-3">
                                <li>
                                    <button type="submit" class="btn btn-primary btn-lg">Save Configuration</button>
                                </li>
                                <li>
                                    <a href="{{ route('admin.apps.index') }}" class="btn btn-outline-light btn-lg">Cancel</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
