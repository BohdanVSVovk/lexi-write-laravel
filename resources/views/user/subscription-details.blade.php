@extends('layouts.user_master')
@section('page_title', __('Subscription Details'))
@section('content')
    <div class="w-[68.9%] 6xl:w-[85.9%] dark:bg-[#292929] flex flex-col flex-1 border-l dark:border-[#474746] border-color-DF subscription-details border-right">
        <div class="xl:flex w-full h-full subscription-main md:overflow-auto sidebar-scrollbar md:h-screen">
            @include('user.includes.account-sidebar')
            @if (!subscription('getUserSubscription', auth()->user()->id))
                <div
                    class="grow 2xl:pl-6 8xl:pr-[84px] px-5 xl:pt-[74px] md:pt-5 pt-[74px] pb-[46px] dark:bg-[#292929] xl:overflow-auto sidebar-scrollbar main-profile-content md:h-screen xl:w-1/2">
                    <a
                        class="flex justify-start items-center font-Figtree text-color-14 dark:text-white text-15 font-normal gap-2.5 md:hidden xl:px-6 pb-4 profile-back cursor-pointer"><svg
                            xmlns="http://www.w3.org/2000/svg" width="16" height="12" viewBox="0 0 16 12" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M15.875 6C15.875 5.68934 15.6232 5.4375 15.3125 5.4375L2.0455 5.4375L5.58525 1.89775C5.80492 1.67808 5.80492 1.32192 5.58525 1.10225C5.36558 0.882582 5.00942 0.882582 4.78975 1.10225L0.289752 5.60225C0.0700827 5.82192 0.0700827 6.17808 0.289752 6.39775L4.78975 10.8977C5.00942 11.1174 5.36558 11.1174 5.58525 10.8977C5.80492 10.6781 5.80492 10.3219 5.58525 10.1023L2.0455 6.5625L15.3125 6.5625C15.6232 6.5625 15.875 6.31066 15.875 6Z"
                                fill="currentColor" />
                        </svg> <span>{{ __('Subscriptions') }}</span> </a>
                    <div>
                        <p class="font-semibold text-color-14 dark:text-white text-20 pb-3">{{ __('Subscription') }}</p>
                        <div class="border-b border-color-DF dark:border-[#474746]"></div>
                    </div>
                    <div class="mt-6 flex 8xl:flex-row flex-col justify-between gap-4 w-full">
                        @include('user.includes.current-plan')
                        <div
                            class="bg-color-F6 dark:bg-color-3A lg:p-6 p-4 rounded-xl 8xl:w-[66.5%] subscription-profile-card h-[220px] w-full flex flex-col justify-between">
                            <div>
                                <p class="text-color-14 dark:text-white text-20 lg:pr-6 pr-4 font-Figtree font-semibold any-sub-p">
                                    {{ __('You do not have any subscription') }}</p>
                                <p class="mt-3 text-color-14 dark:text-white font-Figtree font-normal text-14 pr-5">
                                    {{ __('Subscribe to our more featured plan for more credits & benefits.') }}
                                </p>
                            </div>
                            <div class="flex mt-[26px] justify-start gap-5 flex-wrap">
                                <a href="{{ route('frontend.pricing') }}" class="magic-bg w-max rounded-xl text-16 text-white font-semibold py-3 px-[25px]">
                                    {{ __('All Plan') }}
                                </a>
                                <div class="fixed z-index-999999 hidden items-center inset-0 bg-color-14 bg-opacity-50 overflow-y-auto upgradePlan-information-modal">
                                    <div class="xxs:m-auto mx-5">
                                        <div class="relative my-5 z-index-999999 md:px-5 px-3 py-5 sm:w-[520px] w-max rounded-xl bg-white dark:bg-[#3A3A39] modal-h modal-box-shadow transition-all ease-in-out billing-modal-main" id="billing-modal-main">
                                            <svg class="absolute top-2.5 right-2.5 text-color-14 dark:text-white modal-close-btn p-[1px] cursor-pointer modal-cross" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M3.00749 3.00773C3.41754 2.59768 4.08236 2.59768 4.49241 3.00773L8.99995 7.51527L13.5075 3.00773C13.9175 2.59768 14.5824 2.59768 14.9924 3.00773C15.4025 3.41778 15.4025 4.08261 14.9924 4.49266L10.4849 9.0002L14.9924 13.5077C15.4025 13.9178 15.4025 14.5826 14.9924 14.9927C14.5824 15.4027 13.9175 15.4027 13.5075 14.9927L8.99995 10.4851L4.49241 14.9927C4.08236 15.4027 3.41754 15.4027 3.00749 14.9927C2.59744 14.5826 2.59744 13.9178 3.00749 13.5077L7.51503 9.0002L3.00749 4.49266C2.59744 4.08261 2.59744 3.41778 3.00749 3.00773Z" fill="currentColor"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-10">
                        <p class="font-semibold text-color-14 dark:text-white text-20 pb-3">{{ __('Billing & Payment') }}
                        </p>
                        <div class="border-b border-color-DF dark:border-[#474746]"></div>
                    </div>
                    <div class="mt-6">
                        <div
                            class="bg-color-F6 dark:bg-color-3A rounded-xl p-6 8xl:w-[66.5%] subscription-profile-card">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-Figtree text-color-14 dark:text-white text-20 font-semibold">
                                        {{ auth()->user()->name }}</p>
                                    <p class="font-Figtree text-15 text-color-89 font-medium pt-2">
                                        {{ config('openAI.is_demo') ? 'xxxxxxx@xx.xx' : auth()->user()->email }}</p>
                                </div>
                                <img class="w-[67px] h-[67px] rounded-full pr-0.5"
                                    src="{{ auth()->user()->fileUrl() }}"
                                    alt="{{ __('Image') }}">
                            </div>
                            <div class="mt-11 flex flex-wrap items-center gap-4  justify-start">

                                <div class="">
                                    <p class="font-normal text-13 font-Figtree text-color-14 dark:text-white">
                                        {{ __('Billing Price') }}</p>
                                    <p class="font-semibold text-16 font-Figtree text-color-14 dark:text-white pt-1">
                                        0.00
                                    </p>
                                </div>
                                <div class="">
                                    <p class="font-normal text-13 font-Figtree text-color-14 dark:text-white">
                                        {{ __('Billing Cycle') }}</p>
                                    <p class="font-semibold text-16 font-Figtree text-color-14 dark:text-white pt-1">
                                        ...
                                    </p>
                                </div>
                                <div class="">
                                    <p class="font-normal text-13 font-Figtree text-color-14 dark:text-white">
                                        {{ __('Payment Status') }}</p>
                                    <p class="font-semibold text-16 font-Figtree text-color-14 dark:text-white pt-1">
                                        ...
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10">
                        <p class="font-semibold text-color-14 dark:text-white text-20 pb-3">{{ __('Unsubscribe') }}
                        </p>
                        <div class="border-b border-color-DF dark:border-[#474746]"></div>
                    </div>
                    <div class="pt-6 pb-24">
                        <p class="text-color-14 dark:text-white font-normal font-Figtree text-15 6xl:w-[650px] 4xl:w-[500px] xl:w-[400px]">
                           {{ __('Cancelling your subscription will not cause you to lose all your credits and plan benefits. But you can subscribe again anytime and get to keep all your saved documents & history.')}}
                        </p>
                        <a href="javaScript:void(0);"
                            class="text-color-14 dark:text-white rounded-xl px-[18px] whitespace-nowrap py-[12px] text-15 mt-6 mb-10 flex w-max border border-color-89 dark:border-color-47 bg-color-F6 dark:bg-color-47 font-semibold modal-toggle cursor-default">{{ __('Cancel Subscription') }}</a>
                    </div>
                </div>
            @else
                <div
                    class="grow 2xl:pl-6 8xl:pr-[84px] px-5 xl:pt-[74px] md:pt-5 pt-[74px] pb-[46px] dark:bg-[#292929] xl:overflow-auto sidebar-scrollbar main-profile-content md:h-screen xl:w-1/2">
                   <div class="flex justify-start items-center font-Figtree text-color-14 dark:text-white text-15 font-normal gap-2.5 md:hidden xl:px-6 pb-4">
                        <a class="profile-back cursor-pointer">
                            <svg class="neg-transition-scale" xmlns="http://www.w3.org/2000/svg" width="16" height="12" viewBox="0 0 16 12" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M15.875 6C15.875 5.68934 15.6232 5.4375 15.3125 5.4375L2.0455 5.4375L5.58525 1.89775C5.80492 1.67808 5.80492 1.32192 5.58525 1.10225C5.36558 0.882582 5.00942 0.882582 4.78975 1.10225L0.289752 5.60225C0.0700827 5.82192 0.0700827 6.17808 0.289752 6.39775L4.78975 10.8977C5.00942 11.1174 5.36558 11.1174 5.58525 10.8977C5.80492 10.6781 5.80492 10.3219 5.58525 10.1023L2.0455 6.5625L15.3125 6.5625C15.6232 6.5625 15.875 6.31066 15.875 6Z"
                                fill="currentColor" />
                            </svg>
                        </a>
                        <span>{{ __('Subscriptions') }}</span>
                   </div>
                    <div>
                        <p class="font-semibold text-color-14 dark:text-white text-20 pb-3">{{ __('Subscription') }}</p>
                        <div class="border-b border-color-DF dark:border-[#474746]"></div>
                    </div>
                    <div class="mt-6 flex 8xl:flex-row flex-col justify-between gap-4 w-full">
                        @include('user.includes.current-plan')
                        <div
                            class="bg-color-F6 dark:bg-color-3A lg:p-6 p-4 ltr:!pr-0 rounded-xl upgrade-plan-card 9xl:w-[34.9%] 8xl:w-[42%] w-full flex flex-col justify-between rtl:pl-2">
                           <div>
                                <p class="text-color-14 dark:text-white text-20 lg:pr-6 pr-4 font-Figtree font-semibold any-sub-p">
                                    {{ __('Running out of credits too soon?') }}</p>
                                <p class="mt-3 text-color-14 dark:text-white font-Figtree font-normal text-14 pr-5">
                                    {{ __('Upgrade to our more featured plan for more credits & benefits.') }}
                                </p>
                           </div>
                            <div class="flex mt-[26px] justify-start gap-5 flex-wrap">
                                @if ($activeSubscription->package?->renewable)
                                <button class="magic-bg h-max upgrade-plan w-max rounded-xl text-16 text-white font-semibold py-3 px-[25px] whitespace-nowrap mr-2">
                                    {{ __('Renew Plan') }}
                                </button>
                                @endif
                                <a href="{{ route('frontend.pricing') }}" class="magic-bg w-max rounded-xl text-16 text-white font-semibold py-3 px-[25px]">
                                    {{ __('All Plan') }}
                                </a>
                                @if ($activeSubscription->package)
                                <div class="fixed z-index-999999 hidden items-center inset-0 bg-color-14 bg-opacity-50 overflow-y-auto upgradePlan-information-modal">
                                    <div class="m-auto">
                                        <div class="relative my-5 z-index-999999 md:px-5 px-3 py-5 sm:w-[520px] w-max rounded-xl bg-white dark:bg-[#3A3A39] modal-h modal-box-shadow transition-all ease-in-out billing-modal-main" id="billing-modal-main">
                                            <svg class="absolute top-2.5 right-2.5 text-color-14 dark:text-white modal-close-btn p-[1px] cursor-pointer modal-cross" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M3.00749 3.00773C3.41754 2.59768 4.08236 2.59768 4.49241 3.00773L8.99995 7.51527L13.5075 3.00773C13.9175 2.59768 14.5824 2.59768 14.9924 3.00773C15.4025 3.41778 15.4025 4.08261 14.9924 4.49266L10.4849 9.0002L14.9924 13.5077C15.4025 13.9178 15.4025 14.5826 14.9924 14.9927C14.5824 15.4027 13.9175 15.4027 13.5075 14.9927L8.99995 10.4851L4.49241 14.9927C4.08236 15.4027 3.41754 15.4027 3.00749 14.9927C2.59744 14.5826 2.59744 13.9178 3.00749 13.5077L7.51503 9.0002L3.00749 4.49266C2.59744 4.08261 2.59744 3.41778 3.00749 3.00773Z" fill="currentColor"/>
                                            </svg>
                                            <div class="upgradePlan-modal-container">
                                                <p class="font-Figtree text-color-14 dark:text-white text-20 font-semibold text-left border-b border-color-DF dark:border-color-47 pb-3">
                                                    {{ __("Current Plan") }}</p>
                                                <div class="mt-6 mb-7">
                                                    <div class="grid xxs:grid-cols-2 bg-color-F6 dark:bg-color-47 p-5 rounded-lg gap-4">
                                                        <div>
                                                            <p class="text-color-89 font-semibold text-16 font-Figtree">{{ __("Plan") }}</p>

                                                            <p class="mt-1.5 heading-1 xs:text-28 text-lg font-semibold font-Figtree xxs:w-[130px] sm:w-full">{{ $activePackage->name }}</p>
                                                        </div>
                                                        
                                                        <div>
                                                            <p class="text-color-89 font-semibold text-16 font-Figtree">{{ __("Amount") }}</p>
                                                            <p class="mt-1.5 text-color-14 dark:text-white xs:text-28 text-lg font-semibold font-Figtree xxs:w-[130px] sm:w-full">
                                                                {{ $activeSubscription->package?->discount_price[$activeSubscription->billing_cycle] > 0 ? formatNumber($activeSubscription->package?->discount_price[$activeSubscription->billing_cycle]) : formatNumber($activeSubscription->package?->sale_price[$activeSubscription->billing_cycle]) }}
                                                                <span class="text-14 font-medium">/{{ ($activeSubscription->billing_cycle == 'days' ? $activePackage->duration . ' ' : '') . ucfirst($activeSubscription->billing_cycle) }}</span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="flex flex-col gap-4 mt-6 sub-modal-rtl h-80 pr-6 overflow-auto sidebar-scrollbar">
                                                        @foreach ($activePackageDescription['features'] as $key => $feature)
                                                            @if ($feature->is_visible)
                                                                <div
                                                                    class="flex items-center text-color-14 dark:text-white text-15 font-normal font-Figtree gap-[9px]">
                                                                    <svg class="neg-transition-scale" xmlns="http://www.w3.org/2000/svg" width="12" height="9"
                                                                        viewBox="0 0 12 9" fill="none">
                                                                        <path
                                                                            d="M11.1433 1.10826C11.4609 1.42579 11.4609 1.94146 11.1433 2.25899L4.64036 8.76197C4.32283 9.0795 3.80717 9.0795 3.48964 8.76197L0.238146 5.51048C-0.0793821 5.19295 -0.0793821 4.67728 0.238146 4.35976C0.555675 4.04223 1.07134 4.04223 1.38887 4.35976L4.06627 7.03462L9.99516 1.10826C10.3127 0.790735 10.8284 0.790735 11.1459 1.10826H11.1433Z"
                                                                            fill="url(#paint0_linear_950_2001)" />
                                                                        <defs>
                                                                            <linearGradient id="paint0_linear_950_2001" x1="7.39992"
                                                                                y1="7.99947" x2="5.20783" y2="1.07424"
                                                                                gradientUnits="userSpaceOnUse">
                                                                                <stop offset="0" stop-color="#E60C84" />
                                                                                <stop offset="1" stop-color="#FFCF4B" />
                                                                            </linearGradient>
                                                                        </defs>
                                                                    </svg>
                                                                    <span>
                                                                        @if ($feature->type != 'number')
                                                                            {{ $feature->title }}
                                                                        @elseif ($feature->title_position == 'before')
                                                                            {{ $feature->title . ': ' }}
                                                                            {{ $feature->value == -1 ? __('Unlimited') : $feature->value }}
                                                                        @else
                                                                            {{ $feature->value == -1 ? __('Unlimited') : $feature->value }}
                                                                            {{ $feature->title }}
                                                                        @endif
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @if (preference('apply_coupon_subscription') && (($activePackage['sale_price'][$activeSubscription->billing_cycle] > 0 && !$activePackage['trial_day']) || ($activePackage['trial_day'] && subscription('isUsedTrial', $activePackage['id']))))
                                                <form action="{{ route('user.subscription.checkout') }}" method="GET" class="plan-form">
                                                @else
                                                <form action="{{ route('user.subscription.store') }}" method="POST" class="button-need-disable">
                                                    @csrf
                                                @endif
                                                    <input type="hidden" name="package_id" value="{{ $activePackage->id }}">
                                                    <input type="hidden" name="sending_url" value="{{ techEncrypt(route('user.subscription.store')) }}">
                                                    <input type="hidden" name="billing_cycle" value="{{ $activeSubscription->billing_cycle }}">
                                                    <button type="submit" class="font-Figtree text-white font-semibold text-15 py-[11px] px-10 bg-color-14 rounded-xl flex justify-center items-center gap-3">{{ __("Update") }}
                                                        <span class="items-center update-plan-loader hidden">
                                                            <svg class="animate-spin h-5 w-5 m-auto" xmlns="http://www.w3.org/2000/svg" width="72"
                                                                height="72" viewBox="0 0 72 72" fill="none">
                                                                <mask id="path-1-inside-1_1032_3036" fill="white">
                                                                    <path
                                                                        d="M67 36C69.7614 36 72.0357 38.2493 71.6534 40.9841C70.685 47.9121 67.7119 54.4473 63.048 59.7573C57.2779 66.3265 49.3144 70.5713 40.644 71.6992C31.9736 72.8271 23.1891 70.761 15.9304 65.8866C8.67173 61.0123 3.4351 53.6628 1.19814 45.2104C-1.03881 36.7579 -0.123172 27.7803 3.77411 19.9534C7.67139 12.1266 14.2839 5.98568 22.3772 2.67706C30.4704 -0.631565 39.4912 -0.881694 47.7554 1.97337C54.4353 4.28114 60.2519 8.49021 64.5205 14.0322C66.2056 16.2199 65.3417 19.2997 62.9417 20.6656L60.8567 21.8524C58.4567 23.2183 55.4379 22.3325 53.5977 20.2735C50.9338 17.2927 47.5367 15.0161 43.7066 13.6929C38.2888 11.8211 32.3749 11.9851 27.0692 14.1542C21.7634 16.3232 17.4284 20.3491 14.8734 25.4802C12.3184 30.6113 11.7181 36.4969 13.1846 42.0381C14.6511 47.5794 18.0842 52.3975 22.8428 55.5931C27.6014 58.7886 33.3604 60.1431 39.0445 59.4037C44.7286 58.6642 49.9494 55.8814 53.7321 51.5748C56.4062 48.5302 58.2325 44.8712 59.0732 40.9628C59.6539 38.2632 61.8394 36 64.6008 36H67Z" />
                                                                </mask>
                                                                <path
                                                                    d="M67 36C69.7614 36 72.0357 38.2493 71.6534 40.9841C70.685 47.9121 67.7119 54.4473 63.048 59.7573C57.2779 66.3265 49.3144 70.5713 40.644 71.6992C31.9736 72.8271 23.1891 70.761 15.9304 65.8866C8.67173 61.0123 3.4351 53.6628 1.19814 45.2104C-1.03881 36.7579 -0.123172 27.7803 3.77411 19.9534C7.67139 12.1266 14.2839 5.98568 22.3772 2.67706C30.4704 -0.631565 39.4912 -0.881694 47.7554 1.97337C54.4353 4.28114 60.2519 8.49021 64.5205 14.0322C66.2056 16.2199 65.3417 19.2997 62.9417 20.6656L60.8567 21.8524C58.4567 23.2183 55.4379 22.3325 53.5977 20.2735C50.9338 17.2927 47.5367 15.0161 43.7066 13.6929C38.2888 11.8211 32.3749 11.9851 27.0692 14.1542C21.7634 16.3232 17.4284 20.3491 14.8734 25.4802C12.3184 30.6113 11.7181 36.4969 13.1846 42.0381C14.6511 47.5794 18.0842 52.3975 22.8428 55.5931C27.6014 58.7886 33.3604 60.1431 39.0445 59.4037C44.7286 58.6642 49.9494 55.8814 53.7321 51.5748C56.4062 48.5302 58.2325 44.8712 59.0732 40.9628C59.6539 38.2632 61.8394 36 64.6008 36H67Z"
                                                                    stroke="url(#paint0_linear_1032_3036)" stroke-width="24"
                                                                    mask="url(#path-1-inside-1_1032_3036)" />
                                                                <defs>
                                                                    <linearGradient id="paint0_linear_1032_3036" x1="46.8123" y1="63.1382" x2="21.8195"
                                                                        y2="6.73779" gradientUnits="userSpaceOnUse">
                                                                        <stop offset="0" stop-color="#E60C84" />
                                                                        <stop offset="1" stop-color="#FFCF4B" />
                                                                    </linearGradient>
                                                                </defs>
                                                            </svg>
                                                        </span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="fixed z-index-999999 hidden items-center inset-0 bg-color-14 bg-opacity-50 overflow-y-auto upgradePlan-information-modal">
                                    <div class="m-auto">
                                        <div class="relative my-5 z-index-999999 md:px-5 px-3 py-5 sm:w-[520px] w-max rounded-xl bg-white dark:bg-[#3A3A39] modal-h modal-box-shadow transition-all ease-in-out billing-modal-main" id="billing-modal-main">
                                            {{ __('The Plan is not available.') }}
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-10">
                        <p class="font-semibold text-color-14 dark:text-white text-20 pb-3">{{ __('Billing & Payment') }}
                        </p>
                        <div class="border-b border-color-DF dark:border-[#474746]"></div>
                    </div>
                    <div class="mt-6">
                        <div
                            class="bg-color-F6 dark:bg-color-3A rounded-xl p-6 8xl:w-[66.5%] subscription-profile-card">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-Figtree text-color-14 dark:text-white text-20 font-semibold">
                                        {{ auth()->user()->name }}</p>
                                    <p class="font-Figtree text-15 text-color-89 font-medium pt-2">
                                        {{ config('openAI.is_demo') ? 'xxxxxxx@xx.xx' : auth()->user()->email }}</p>
                                </div>
                                <img class="w-[67px] h-[67px] rounded-full pr-0.5"
                                    src="{{ auth()->user()->fileUrl() }}"
                                    alt="{{ __('Image') }}">
                            </div>
                            <div class="mt-11 flex flex-wrap items-center 3xl:gap-10 gap-4 justify-start 6xl:w-[500px] 4xl:w-[450px] xl:w-[400px]">
                                <div>
                                    <p class="font-normal text-13 font-Figtree text-color-14 dark:text-white">
                                        {{ __('Billing Price') }}</p>
                                    <p class="font-semibold text-16 font-Figtree text-color-14 dark:text-white pt-1">
                                        {{ formatNumber($activeSubscription->amount_billed) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="font-normal text-13 font-Figtree text-color-14 dark:text-white">
                                        {{ __('Billing Cycle') }}</p>
                                    <p class="font-semibold text-16 font-Figtree text-color-14 dark:text-white pt-1">
                                        {{ ($activeSubscription->billing_cycle == 'days' ? $activeSubscription->duration . ' ' : '') . ucFirst($activeSubscription->billing_cycle) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="font-normal text-13 font-Figtree text-color-14 dark:text-white">
                                        {{ __('Payment Status') }}</p>
                                    <p class="font-semibold text-16 font-Figtree text-color-14 dark:text-white pt-1">
                                        {{ ucFirst($activeSubscription->payment_status) }}
                                    </p>
                                </div>
                                @if (subscription('isTrialMode', $activeSubscription->id) || ($activeSubscription->status != 'Pending' && $activeSubscription->billing_cycle != 'lifetime'))
                                <div>
                                    <p class="font-normal text-13 font-Figtree text-color-14 dark:text-white">
                                        {{ $activeSubscription->status == 'Cancel' ? __('Expired Date') : __('Next Billing Date') }}</p>
                                    <p class="font-semibold text-16 font-Figtree text-color-14 dark:text-white pt-1">
                                        {{ timezoneFormatDate($activeSubscription->next_billing_date) }}
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-10">
                        <p class="font-semibold text-color-14 dark:text-white text-20 pb-3">{{ __('Unsubscribe') }}
                        </p>
                        <div class="border-b border-color-DF dark:border-[#474746]"></div>
                    </div>
                    <div class="pt-6 pb-24">
                        <p class="text-color-14 dark:text-white font-normal font-Figtree text-15 6xl:w-[650px] 4xl:w-[500px] xl:w-[400px]">
                            {{ __('Cancelling your subscription will not cause you to lose your current existing credits and plan benefits. But you can subscribe again anytime and get to keep all your saved documents & history.')}}
                        </p>
                        @php
                            $isActiveSubscription = subscription('getUserSubscription', auth()->user()->id)->status == 'Active';
                        @endphp

                        <a href="javaScript:void(0);" title="{{ $isActiveSubscription ? '' : __('Cancellable plans are limited to only active subscriptions.') }}"
                            class="{{ $isActiveSubscription ? '' : 'cancel-tooltip' }} text-color-14 dark:text-white rounded-xl px-[18px] whitespace-nowrap py-[12px] text-15 mt-6 mb-10 flex w-max border border-color-89 dark:border-color-47 bg-color-F6 dark:bg-color-47 font-semibold modal-toggle {{ $isActiveSubscription ? '' : 'cursor-default' }}">{{ __('Cancel Subscription') }}</a>
                        <div class="modal {{ $isActiveSubscription ? 'index-modal' : '' }}  absolute z-50 top-0 left-0 right-0 w-full h-full">
                            <div class="modal-overlay fixed z-10 top-0 right-0 left-0 w-full h-full">
                            </div>
                            <div class="modal-wrapper modal-wrapper modal-transition fixed inset-0 z-10">
                                <div class="modal-body flex h-full justify-center p-4 text-center items-center sm:p-0">
                                    <div class="modal-content modal-transition rounded-xl py-6 md:px-[54px] bg-white dark:bg-color-3A px-8">
                                        <p class="text-color-14 font-semibold text-20 font-Figtree dark:text-white text-center">{{ __('Cancel Subscription') }}?</p>
                                        <p class="font-Figtree text-color-14 dark:text-white text-15 font-normal mt-3 text-center md:w-[332px]">
                                            {{ __('You will not lose any of your existing credits or plan benefits.') }}
                                        </p>
                                        <div class="flex justify-center items-center mt-7 gap-[16px]">
                                            <a href="javaScript:void(0);" class="font-Figtree text-color-14 dark:text-white font-semibold xs:text-15 text-14 py-[11px] xs:px-[42px] px-[30px] border border-color-89 dark:border-color-47 bg-color-F6 dark:bg-color-47 rounded-xl modal-toggle">{{ __("Not Really") }}</a>
                                            <a href="{{ route('user.subscription.cancel', ['user_id' => auth()->user()->id]) }}" class="font-Figtree text-white font-semibold xs:text-15 text-14 py-[11px] xs:px-[30px] px-5 bg-color-DFF rounded-xl">{{ __('Yes, Cancel') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('public/assets/js/user/subscription.min.js') }}"></script>
@endsection
