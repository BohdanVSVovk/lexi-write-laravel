@extends('gateway::layouts.payment')

@section('logo', asset(config('coinbase.logo')))
@section('gateway', config('coinbase.name'))

@section('content')
    @include('gateway::partial.instruction')
    <form action="{{ route('gateway.complete', withOldQueryIntegrity(['gateway' => config('coinbase.alias')])) }}"
        method="post" id="payment-form" class="pay-form">
        @csrf
        <button type="submit" class="pay-button sub-btn">{{ __('Pay With Coinbase') }}</button>
    </form>
@endsection
