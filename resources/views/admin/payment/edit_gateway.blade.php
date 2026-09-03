@extends('layouts.admin')

@section('title', 'Edit Gateway | ' . $gateway->name)
@section('page_title', 'Edit Payment Gateway: ' . $gateway->name)
@section('page_subtitle', 'Update global settings for this payment provider.')

@section('content')
<div class="nk-block">
    <div class="card card-bordered">
        <div class="card-inner">
            <form action="{{ route('admin.payment.gateways.update', $gateway->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row gy-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="name">Gateway Name</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" id="name" name="name" value="{{ $gateway->name }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="is_active">Status</label>
                            <div class="form-control-wrap">
                                <select class="form-select js-select2" id="is_active" name="is_active">
                                    <option value="1" {{ $gateway->is_active ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$gateway->is_active ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <hr class="preview-hr">
                        <h6 class="title">Configuration Settings</h6>
                        <p class="text-soft">Define default configuration for this gateway.</p>
                    </div>

                    @if($gateway->slug === 'paystack')
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Base URL</label>
                            <input type="text" name="config[base_url]" class="form-control" value="{{ $gateway->config['base_url'] ?? 'https://api.paystack.co' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Default Currency</label>
                            <input type="text" name="config[currency]" class="form-control" value="{{ $gateway->config['currency'] ?? 'NGN' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Default Public Key</label>
                            <input type="text" name="config[public_key]" class="form-control" value="{{ $gateway->config['public_key'] ?? '' }}" placeholder="pk_test_...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Default Secret Key</label>
                            <input type="password" name="config[secret_key]" class="form-control" value="{{ $gateway->config['secret_key'] ?? '' }}" placeholder="sk_test_...">
                        </div>
                    </div>
                    @elseif($gateway->slug === 'flutterwave')
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Base URL</label>
                            <input type="text" name="config[base_url]" class="form-control" value="{{ $gateway->config['base_url'] ?? 'https://api.flutterwave.com/v3' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Default Currency</label>
                            <input type="text" name="config[currency]" class="form-control" value="{{ $gateway->config['currency'] ?? 'NGN' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Default Public Key</label>
                            <input type="text" name="config[public_key]" class="form-control" value="{{ $gateway->config['public_key'] ?? '' }}" placeholder="FLWPUBK_TEST-...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Default Secret Key</label>
                            <input type="password" name="config[secret_key]" class="form-control" value="{{ $gateway->config['secret_key'] ?? '' }}" placeholder="FLWSECK_TEST-...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Encryption Key</label>
                            <input type="password" name="config[encryption_key]" class="form-control" value="{{ $gateway->config['encryption_key'] ?? '' }}">
                        </div>
                    </div>
                    @endif

                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label" for="description">Description</label>
                            <div class="form-control-wrap">
                                <textarea class="form-control no-resize" id="description" name="description">{{ $gateway->description }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <ul class="align-center flex-wrap g-3">
                            <li>
                                <button type="submit" class="btn btn-primary">Update Gateway</button>
                            </li>
                            <li>
                                <a href="{{ route('admin.payment.index') }}" class="btn btn-light">Cancel</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
