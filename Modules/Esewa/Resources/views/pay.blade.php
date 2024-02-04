@extends('gateway::layouts.payment')

@section('logo', asset(config('esewa.logo')))
@section('gateway', config('esewa.name'))

@section('content')
    <div class="straight-line"></div>
    @include('gateway::partial.instruction')
    <form class="pay-form needs-validation"
        action="{{ route('gateway.complete', withOldQueryIntegrity(['gateway' => config('esewa.alias')])) }}" method="post"
        id="payment-form">
        @csrf
        <div>
            <!-- Used to display form errors -->
            <div id="card-errors"></div>
        </div>
        <button type="submit" class="pay-button sub-btn">{{ __('Pay With Esewa') }}</button>
    </form>
@endsection
