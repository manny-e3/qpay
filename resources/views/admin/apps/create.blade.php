@extends('layouts.admin')

@section('title', 'Add Application | OTP Cloud')
@section('page_title', 'Add Application')
@section('page_subtitle', 'Create a new client application and configure its OTP settings.')

@section('content')
<div class="nk-block">
    <div class="card card-bordered">
        <div class="card-inner">
            <form action="{{ route('admin.apps.store') }}" method="POST" class="gy-3">
                @csrf
                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="form-label" for="appName">Application Name</label>
                            <span class="form-note">Specify a name for this client application.</span>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <input type="text" name="appName" class="form-control @error('appName') is-invalid @enderror" id="appName" value="{{ old('appName') }}" placeholder="e.g. My Web Portal" required>
                                @error('appName')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="form-label" for="appID">Application ID</label>
                            <span class="form-note">Unique numeric identifier for the app.</span>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <input type="text" name="appID" class="form-control @error('appID') is-invalid @enderror" id="appID" value="{{ old('appID') }}" placeholder="e.g. 101" required>
                                @error('appID')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="form-label" for="username">API Username</label>
                            <span class="form-note">Used for API authentication.</span>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" id="username" value="{{ old('username') }}" required>
                                @error('username')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="form-label" for="password">API Password</label>
                            <span class="form-note">Secure password for API access.</span>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" required>
                                @error('password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="form-label" for="type">OTP Type</label>
                            <span class="form-note">The character set used for OTP generation.</span>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <select name="type" class="form-select js-select2 @error('type') is-invalid @enderror" id="type" required>
                                    <option value="numeric" {{ old('type') == 'numeric' ? 'selected' : '' }}>Numeric (0-9)</option>
                                    <option value="alphabetic" {{ old('type') == 'alphabetic' ? 'selected' : '' }}>Alphabetic (A-Z)</option>
                                    <option value="alphanumeric" {{ old('type') == 'alphanumeric' ? 'selected' : '' }}>Alphanumeric (A-Z, 0-9)</option>
                                </select>
                                @error('type')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="form-label" for="otp_length">OTP Length</label>
                            <span class="form-note">Number of characters in the generated OTP.</span>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <input type="number" name="otp_length" class="form-control @error('otp_length') is-invalid @enderror" id="otp_length" value="{{ old('otp_length', 4) }}" min="4" max="10" required>
                                @error('otp_length')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="form-label" for="status">Initial Status</label>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <select name="status" class="form-select js-select2" id="status" required>
                                    <option value="Active" {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="preview-hr">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-head-content">
                        <h6 class="title">Email Template Customization</h6>
                    </div>
                </div>

                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="form-label" for="email_subject">Email Subject</label>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <input type="text" name="email_subject" class="form-control @error('email_subject') is-invalid @enderror" id="email_subject" value="{{ old('email_subject', 'Your OTP Code') }}">
                                @error('email_subject')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="form-label" for="email_body">Email Body</label>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <textarea name="email_body" class="form-control no-resize @error('email_body') is-invalid @enderror" id="email_body" rows="3">{{ old('email_body', 'Your one-time password is: [OTP]') }}</textarea>
                                @error('email_body')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="form-label" for="link">Portal Link</label>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <input type="url" name="link" class="form-control @error('link') is-invalid @enderror" id="link" value="{{ old('link') }}" placeholder="https://example.com/verify">
                                @error('link')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="form-label" for="admin_email">Admin Notification Email</label>
                            <span class="form-note">Receive connection details at this address.</span>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <input type="email" name="admin_email" class="form-control @error('admin_email') is-invalid @enderror" id="admin_email" value="{{ old('admin_email') }}" placeholder="admin@example.com">
                                @error('admin_email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="preview-hr">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-head-content">
                        <h6 class="title">Payment Integration (Optional)</h6>
                        <p>Enable one or more payment gateways for this application.</p>
                    </div>
                </div>

                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="form-label">Select Gateways</label>
                            <span class="form-note">Choose providers to enable payments.</span>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <ul class="custom-control-group g-3 align-center flex-wrap">
                                    @foreach($gateways as $gateway)
                                    <li>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="gateways[]" value="{{ $gateway->id }}" 
                                                class="custom-control-input" id="gateway-{{ $gateway->id }}"
                                                {{ is_array(old('gateways')) && in_array($gateway->id, old('gateways')) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="gateway-{{ $gateway->id }}">{{ $gateway->name }}</label>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                                @if($gateways->isEmpty())
                                    <span class="text-soft italic">No active gateways found. <a href="{{ route('admin.payment.index') }}">Manage Gateways</a></span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div id="callback_field" class="row g-3 align-center {{ old('gateways') ? '' : 'd-none' }}">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="form-label" for="payment_callback_url">Common Callback URL</label>
                            <span class="form-note">Applied to all selected gateways. You can customize later.</span>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <input type="url" name="payment_callback_url" class="form-control @error('payment_callback_url') is-invalid @enderror" id="payment_callback_url" value="{{ old('payment_callback_url') }}" placeholder="https://yourapp.com/callback">
                                @error('payment_callback_url')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row g-3">
                    <div class="col-lg-7 offset-lg-5">
                        <div class="form-group mt-2">
                            <button type="submit" class="btn btn-lg btn-primary">Create Application</button>
                            <a href="{{ route('admin.apps.index') }}" class="btn btn-lg btn-light ml-1">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('input[name="gateways[]"]').on('change', function() {
            if ($('input[name="gateways[]"]:checked').length > 0) {
                $('#callback_field').removeClass('d-none');
            } else {
                $('#callback_field').addClass('d-none');
            }
        });
    });
</script>
@endpush
