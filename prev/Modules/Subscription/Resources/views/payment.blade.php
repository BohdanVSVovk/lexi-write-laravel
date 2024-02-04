@extends('admin.layouts.app')
@section('page_title', __('Payment'))

@section('content')

<!-- Main content -->
<div class="col-sm-12 list-container" id="payment-list-container">
    <div class="card">
        <div class="card-header d-md-flex justify-content-between align-items-center">
            <h5>{{ __('Payments') }}</h5>
        </div>
        <div class="card-body px-4 product-table payment-table">
            <div class="card-block pt-2 px-0">
                <div class="col-sm-12">
                    @include('admin.layouts.includes.yajra-data-table')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('Modules/Subscription/Resources/assets/js/subscription.min.js') }}"></script>
@endsection
