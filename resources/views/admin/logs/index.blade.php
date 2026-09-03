@extends('layouts.admin')

@section('title', 'Audit Logs | OTP Cloud')
@section('page_title', 'Audit Logs')
@section('page_subtitle', 'Track and monitor all OTP generation and validation activity.')

@section('content')
<div class="nk-block">
    <div class="card card-bordered card-stretch">
        <div class="card-inner-group">
            <div class="card-inner position-relative card-tools-toggle">
                <div class="card-title-group">
                    <div class="card-tools">
                        <form action="{{ route('admin.logs.index') }}" method="GET">
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-left">
                                    <em class="icon ni ni-search"></em>
                                </div>
                                <input type="text" name="search" class="form-control" placeholder="Search logs..." value="{{ request('search') }}">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-inner p-0">
                <table class="table table-tranx">
                    <thead>
                        <tr class="tb-tnx-head">
                            <th class="tb-tnx-id"><span class="overline-title">Date</span></th>
                            <th class="tb-tnx-info"><span class="overline-title">App / User</span></th>
                            <th class="tb-tnx-info"><span class="overline-title">IP / Proxy</span></th>
                            <th class="tb-tnx-status"><span class="overline-title">Status</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr class="tb-tnx-item">
                            <td class="tb-tnx-id">
                                <span class="text-soft small">{{ $log->created_at->format('M d, Y H:i') }}</span>
                            </td>
                            <td class="tb-tnx-info">
                                <div class="tb-tnx-desc">
                                    <span class="title">{{ $log->username }}</span>
                                    <span class="sub-text">App: {{ $log->appID }}</span>
                                </div>
                            </td>
                            <td class="tb-tnx-info">
                                <div class="tb-tnx-desc">
                                    <span class="title">{{ $log->IP }}</span>
                                </div>
                            </td>
                            <td class="tb-tnx-status">
                                <span class="badge badge-dim {{ $log->status === 'validated' ? 'bg-success' : ($log->status === 'expired' ? 'bg-danger' : 'bg-warning') }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-inner">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
