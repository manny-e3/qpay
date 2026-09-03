@extends('layouts.admin')

@section('title', 'Payments | OTP Cloud')
@section('page_title', 'Payment Management')
@section('page_subtitle', 'Manage payment gateways and monitor transactions across all onboarded applications.')

@section('content')
<div class="nk-block">
    <div class="row g-gs">
        <div class="col-md-12">
            <div class="card card-bordered card-full">
                <div class="card-inner border-bottom">
                    <div class="card-title-group">
                        <div class="card-title">
                            <h6 class="title">Supported Gateways</h6>
                        </div>
                    </div>
                </div>
                <div class="card-inner p-0">
                    <table class="table table-tranx">
                        <thead>
                            <tr class="tb-tnx-head">
                                <th class="tb-tnx-id"><span class="overline-title">Name</span></th>
                                <th class="tb-tnx-info"><span class="overline-title">Slug</span></th>
                                <th class="tb-tnx-status"><span class="overline-title">Status</span></th>
                                <th class="tb-tnx-action"><span>&nbsp;</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gateways as $gateway)
                            <tr class="tb-tnx-item">
                                <td class="tb-tnx-id">
                                    <span class="title">{{ $gateway->name }}</span>
                                </td>
                                <td class="tb-tnx-info">
                                    <span class="text-soft">{{ $gateway->slug }}</span>
                                </td>
                                <td class="tb-tnx-status">
                                    <span class="badge badge-dot {{ $gateway->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $gateway->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="tb-tnx-action">
                                    <a href="{{ route('admin.payment.gateways.edit', $gateway->id) }}" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Gateway">
                                        <em class="icon ni ni-edit"></em>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center p-4">No gateways found. Run a seeder to add them.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card card-bordered card-full">
                <div class="card-inner border-bottom">
                    <div class="card-title-group">
                        <div class="card-title">
                            <h6 class="title">Recent Transactions</h6>
                        </div>
                    </div>
                </div>
                <div class="card-inner p-0">
                    <table class="table table-tranx">
                        <thead>
                            <tr class="tb-tnx-head">
                                <th class="tb-tnx-id"><span class="overline-title">Reference</span></th>
                                <th class="tb-tnx-info"><span class="overline-title">App / Customer</span></th>
                                <th class="tb-tnx-amount"><span class="overline-title">Amount</span></th>
                                <th class="tb-tnx-status"><span class="overline-title">Status</span></th>
                                <th class="tb-tnx-date"><span class="overline-title">Date</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                            <tr class="tb-tnx-item">
                                <td class="tb-tnx-id">
                                    <span class="badge badge-dim bg-primary">{{ $transaction->reference }}</span>
                                </td>
                                <td class="tb-tnx-info">
                                    <div class="tb-tnx-desc">
                                        <span class="title">{{ $transaction->app->appName ?? 'N/A' }}</span>
                                    </div>
                                    <div class="tb-tnx-date">
                                        <span class="date">{{ $transaction->customer_email }}</span>
                                    </div>
                                </td>
                                <td class="tb-tnx-amount">
                                    <div class="tb-tnx-total">
                                        <span class="amount">{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</span>
                                    </div>
                                </td>
                                <td class="tb-tnx-status">
                                    <span class="badge badge-dot {{ $transaction->status === 'successful' ? 'bg-success' : ($transaction->status === 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td class="tb-tnx-date">
                                    <span class="date">{{ $transaction->created_at->format('M d, Y H:i') }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center p-4">No transactions found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-inner border-top">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
