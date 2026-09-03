@extends('layouts.admin')

@section('title', 'Global Settings | OTP Cloud')
@section('page_title', 'Settings')
@section('page_subtitle', 'Manage system-wide OTP parameters and dynamic responses.')

@section('content')
<div class="nk-block">
    <div class="row g-gs">
        <div class="col-lg-6">
            <div class="card card-bordered">
                <div class="card-inner">
                    <div class="card-head">
                        <h5 class="card-title">OTP Expiration</h5>
                    </div>
                    <form action="{{ route('admin.settings.update-duration') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label class="form-label" for="duration">Duration (Minutes)</label>
                            <div class="form-control-wrap">
                                <div class="input-group">
                                    <input type="number" name="duration" class="form-control" id="duration" value="{{ $duration->duration }}" required>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </div>
                            <p class="form-note">How long an OTP remains valid after generation.</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-bordered">
                <div class="card-inner">
                    <div class="card-head">
                        <h5 class="card-title">Code Complexity</h5>
                    </div>
                    <form action="{{ route('admin.settings.update-length') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label class="form-label" for="length">OTP Length (Characters)</label>
                            <div class="form-control-wrap">
                                <div class="input-group">
                                    <input type="number" name="length" class="form-control" id="length" value="{{ $length->length }}" min="4" max="12" required>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </div>
                            <p class="form-note">The number of characters in generated OTPs.</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="nk-block nk-block-lg mt-5">
    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="card-head">
                <h5 class="card-title">Dynamic Response Strings</h5>
            </div>
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Event Context</th>
                        <th>API Code</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($responses as $response)
                    <tr>
                        <td><span class="fw-bold">{{ ucfirst(str_replace('_', ' ', $response->message)) }}</span></td>
                        <td><span class="badge badge-dim bg-outline-primary">{{ $response->code }}</span></td>
                        <td class="text-soft small">{{ $response->description }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
