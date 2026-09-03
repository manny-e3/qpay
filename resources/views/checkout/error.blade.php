@extends('layouts.checkout')

@section('content')
<div class="card card-bordered card-stretch">
    <div class="card-inner">
        <div class="text-center p-4">
            <div class="nk-modal-icon nk-modal-icon-lg text-danger">
                <em class="icon ni ni-alert-fill"></em>
            </div>
            <h4 class="nk-modal-title">Checkout Error</h4>
            <p class="sub-text">{{ $message ?? 'An unexpected error occurred during checkout.' }}</p>

            <div class="mt-4">
                <a href="javascript:history.back()" class="btn btn-outline-light">Go Back</a>
            </div>
        </div>
    </div>
</div>
@endsection
