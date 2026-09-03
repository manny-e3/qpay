@extends('layouts.admin')

@section('title', 'Applications | OTP Cloud')
@section('page_title', 'Application Management')
@section('page_subtitle', 'Manage and configure your registered client applications.')

@section('page_actions')
<a href="{{ route('admin.apps.create') }}" class="btn btn-primary d-none d-md-inline-flex"><em class="icon ni ni-plus"></em><span>Add Application</span></a>
@endsection

@section('content')
<div class="nk-block">
    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <table class="datatable-init table">
                <thead>
                    <tr class="tb-tnx-head">
                        <th class="tb-tnx-id"><span class="overline-title">App ID</span></th>
                        <th class="tb-tnx-info">
                            <span class="tb-tnx-desc">
                                <span>Application Name</span>
                            </span>
                        </th>
                        <th class="tb-tnx-id"><span class="overline-title">OTP Type</span></th>
                        <th class="tb-tnx-status"><span class="overline-title">Status</span></th>
                        <th class="tb-tnx-action"><span>&nbsp;</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($apps as $app)
                    <tr class="tb-tnx-item">
                        <td class="tb-tnx-id">
                            <span class="badge badge-dim bg-primary">{{ $app->appID }}</span>
                        </td>
                        <td class="tb-tnx-info">
                            <div class="tb-tnx-desc">
                                <span class="title">{{ $app->appName }}</span>
                            </div>
                        </td>
                        <td class="tb-tnx-id">
                            <span class="text-soft">{{ ucfirst($app->type) }}</span>
                        </td>
                        <td class="tb-tnx-status">
                            <span class="badge badge-dot {{ $app->status === 'Active' ? 'bg-success' : 'bg-danger' }}">
                                {{ $app->status }}
                            </span>
                        </td>
                        <td class="tb-tnx-action">
                            <div class="dropdown">
                                <a class="text-soft dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                    <ul class="link-list-plain">
                                        <li><a href="{{ route('admin.apps.test', $app->id) }}" class="text-primary">Test App</a></li>
                                        <li><a href="{{ route('admin.apps.payment', $app->id) }}" class="text-info">Payment Settings</a></li>
                                        <li><a href="{{ route('admin.apps.edit', $app->id) }}">Edit</a></li>
                                        <li>
                                            <form action="{{ route('admin.apps.destroy', $app->id) }}" method="POST" onsubmit="return confirm('Protect this app from deletion?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-danger border-0 bg-transparent p-0" style="padding: 0.5rem 1rem !important; display: block; width: 100%; text-align: left;">Remove</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
