@extends('gateway::layouts.payment')

@section('logo', asset(config('stripe.logo')))

@section('gateway', config('stripe.name'))

@section('content')
    <div class="straight-line"></div>

    @include('gateway::partial.instruction')

    <form class="pay-form needs-validation"
        action="{{ route('gateway.complete', withOldQueryIntegrity(['gateway' => config('stripe.alias')])) }}" method="post"
        id="payment-form">
        @csrf
        
        <button type="submit" class="pay-button sub-btn">{{ __('Pay With Stripe') }}</button>
    </form>
@endsection

@section('css')

@endsection

@section('js')

@endsection
