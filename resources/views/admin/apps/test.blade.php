@extends('layouts.admin')

@section('title', 'Test Application | OTP Cloud')
@section('page_title', 'Test Application: ' . $app->appName)
@section('page_subtitle', 'Simulate API requests to verify your connection settings.')

@section('content')
<div class="nk-block">
    <div class="row g-gs">
        <div class="col-lg-5">
            <div class="card card-bordered">
                <div class="card-inner">
                    <div class="card-head">
                        <h5 class="card-title">Test Parameters</h5>
                    </div>
                    <form action="{{ route('admin.apps.run-test', $app) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">App ID</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" value="{{ $app->appID }}" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="username">Recipient Email (Username)</label>
                            <div class="form-control-wrap">
                                <input type="email" name="username" class="form-control @error('username') is-invalid @enderror" id="username" value="{{ old('username', 'test@example.com') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="name">Recipient Name</label>
                            <div class="form-control-wrap">
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name', 'Test User') }}" required>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                <em class="icon ni ni-play-fill"></em> <span>Run Test API Call</span>
                            </button>
                            <a href="{{ route('admin.apps.index') }}" class="btn btn-outline-light btn-block mt-2">Back to List</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-bordered mt-4">
                <div class="card-inner">
                    <div class="card-head">
                        <h5 class="card-title">Connection Credentials</h5>
                    </div>
                    <div class="alert alert-light alert-icon">
                        <em class="icon ni ni-info"></em>
                        <p class="small">These credentials must be sent as HTTP Headers in your client application.</p>
                    </div>
                    <div class="row g-2">
                        <div class="col-12">
                            <span class="overline-title">Header: ID</span>
                            <div class="code-block bg-lighter p-2">{{ $app->appID }}</div>
                        </div>
                        <div class="col-12">
                            <span class="overline-title">Header: Username</span>
                            <div class="code-block bg-lighter p-2">{{ $app->username }}</div>
                        </div>
                        <div class="col-12">
                            <span class="overline-title">Header: Password</span>
                            <div class="code-block bg-lighter p-2">{{ $app->password }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            @if(session('test_result'))
                @php $res = session('test_result'); @endphp
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-head d-flex justify-content-between">
                            <h5 class="card-title">API Response</h5>
                            <span class="badge {{ $res['status'] == 200 ? 'badge-success' : 'badge-danger' }} badge-lg">
                                Status: {{ $res['status'] }}
                            </span>
                        </div>
                        
                        <div class="mt-4">
                            <h6 class="overline-title">Endpoint URL</h6>
                            <div class="code-block bg-lighter p-2">{{ $res['url'] }}</div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h6 class="overline-title">Sent Headers</h6>
                                <pre class="p-2 bg-lighter rounded small">@foreach($res['headers'] as $k => $v)<strong>{{ $k }}:</strong> {{ $v }}
@endforeach</pre>
                            </div>
                            <div class="col-md-6">
                                <h6 class="overline-title">Sent Payload</h6>
                                <pre class="p-2 bg-lighter rounded small">{{ json_encode($res['payload'], JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h6 class="overline-title">Response Body</h6>
                            <pre class="p-3 bg-dark text-white rounded">{{ is_array($res['body']) ? json_encode($res['body'], JSON_PRETTY_PRINT) : $res['body'] }}</pre>
                        </div>
                        
                        <div class="mt-2 text-right">
                            <span class="text-soft small">Execution time: {{ $res['duration'] }}</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="card card-bordered h-100 d-flex align-center justify-center bg-lighter border-dashed">
                    <div class="nk-block-content text-center p-5">
                        <em class="icon ni ni-terminal opacity-50 mb-3" style="font-size: 64px;"></em>
                        <h5 class="title">No test run yet</h5>
                        <p class="text-soft">Fill in the recipient details and click "Run Test API Call" to simulate a request from your client application.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
