@extends('gateway::layouts.payment')

@section('logo', asset(config('paypalrecurring.logo')))
@section('gateway', config('paypalrecurring.name'))

@section('content')
    <div class="straight-line"></div>
    @include('gateway::partial.instruction')
    <form action="{{ route('gateway.complete', withOldQueryIntegrity(['gateway' => config('paypalrecurring.alias')])) }}"
        method="post" id="payment-form" class="pay-form">
        @csrf
        <button type="submit" class="pay-button sub-btn">
            <span>{{ __('Pay With Paypal Recurring') }}
        </button>
    </form>
@endsection
