@extends('gateway::layouts.payment')

@section('logo', asset(config('yukassa.logo')))
@section('gateway', config('yukassa.name'))

@section('content')
    <div class="straight-line"></div>
    @include('gateway::partial.instruction')
    <form action="{{ route('gateway.complete', withOldQueryIntegrity(['gateway' => config('yukassa.alias')])) }}"
        method="post" id="payment-form" class="pay-form">
        @csrf
        <button type="submit" class="pay-button sub-btn">
            <span>{{ __('Pay With YuKassa') }}
        </button>
    </form>
@endsection
