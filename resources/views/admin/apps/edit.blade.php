@extends('layouts.admin')

@section('title', 'Edit Application | OTP Cloud')
@section('page_title', 'Edit Application')
@section('page_subtitle', 'Modify configuration for ' . $app->appName)

@section('content')
<div class="nk-block">
    <div class="card card-bordered">
        <div class="card-inner">
            <form action="{{ route('admin.apps.update', $app->id) }}" method="POST" class="gy-3">
                @csrf
                @method('PUT')
                
                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="form-label" for="appName">Application Name</label>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <input type="text" name="appName" class="form-control" id="appName" value="{{ old('appName', $app->appName) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="form-label">App ID (Static)</label>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <input type="text" class="form-control disabled" value="{{ $app->appID }}" disabled>
                                <input type="hidden" name="appID" value="{{ $app->appID }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="form-label" for="username">API Username</label>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <input type="text" name="username" class="form-control" id="username" value="{{ old('username', $app->username) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="form-label" for="password">API Password</label>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <input type="password" name="password" class="form-control" id="password" value="{{ old('password', $app->password) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="form-label" for="type">OTP Type</label>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <select name="type" class="form-select js-select2" id="type" required>
                                    <option value="numeric" {{ old('type', $app->type) == 'numeric' ? 'selected' : '' }}>Numeric (0-9)</option>
                                    <option value="alphabetic" {{ old('type', $app->type) == 'alphabetic' ? 'selected' : '' }}>Alphabetic (A-Z)</option>
                                    <option value="alphanumeric" {{ old('type', $app->type) == 'alphanumeric' ? 'selected' : '' }}>Alphanumeric (A-Z, 0-9)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="form-label" for="status">Status</label>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <select name="status" class="form-select js-select2" id="status" required>
                                    <option value="Active" {{ old('status', $app->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ old('status', $app->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
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
                                <input type="text" name="email_subject" class="form-control" id="email_subject" value="{{ old('email_subject', $app->email_subject) }}">
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
                                <textarea name="email_body" class="form-control no-resize" id="email_body" rows="3">{{ old('email_body', $app->email_body) }}</textarea>
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
                                <input type="url" name="link" class="form-control" id="link" value="{{ old('link', $app->link) }}">
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row g-3">
                    <div class="col-lg-7 offset-lg-5">
                        <div class="form-group mt-2">
                            <button type="submit" class="btn btn-lg btn-primary">Update Application</button>
                            <a href="{{ route('admin.apps.index') }}" class="btn btn-lg btn-light ml-1">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
