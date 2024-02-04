<div class="6xl:gap-10 lg:gap-5 gap-6 lg:px-0 md:px-10 px-5 w-full flex flex-wrap justify-center {{count($packages) != 0 ? 'lg:mb-[140px] mb-[90px] 6xl:mt-[60px] mt-11' : ''}}">
    @foreach($packages as $key => $package)
        <div class="{{ $package['parent_class'] }}">
            <div class="{{ $package['child_class'] }}">
                <p class="text-color-14 dark:text-white text-24 font-medium font-Figtree break-words">{{ $package['name'] }}</p>

                <p class="text-36 font-medium font-RedHat text-color-14 dark:text-white mt-1">
                    @if($package['discount_price'] > 0)
                        <span class="{{ $package['price_color'] }}">{{ formatNumber($package['discount_price']) }}</span>
                    @else
                        <span class="{{ $package['price_color'] }}">{{ $package['sale_price'] == 0 ? __('Free') : formatNumber($package['sale_price']) }}</span>
                    @endif
                    <span class="text-18">/{{ ($package['billing_cycle'] == 'days' ? $package['duration'] . ' ' : '') . ucfirst($package['billing_cycle']) }}</span>
                </p>
                <form action="{{ route('user.subscription.store') }}" method="post" class="button-need-disable">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $package['id'] }}">
                    @if (auth()->user() && $package['trial_day'] && !subscription('isUsedTrial', $package['id']))
                        <button type="submit" class="{{ $package['button'] }} plan-loader flex gap-3">{{ __('Start :x Days Trial', ['x' => $package['trial_day']]) }}</button>
                    @elseif (!$subscription?->package?->id)
                        <button type="submit" class="{{ $package['button'] }} plan-loader flex gap-3">{{ __('Subscribe Now') }}</button>
                    @elseif ($subscription?->package?->id == $package['id'])
                        @if ($subscription?->package?->renewable)
                            <button type="submit" class="{{ $package['button'] }} plan-loader flex gap-3">{{ __('Renew Plan') }}</button>
                        @endif
                    @elseif (preference('subscription_change_plan') && $subscription?->package?->sale_price < $package['sale_price'])
                        <button type="submit" class="{{ $package['button'] }} plan-loader flex gap-3">{{ __('Upgrade Plan') }}</button>
                    @elseif (preference('subscription_change_plan') && preference('subscription_downgrade') && $subscription?->package?->sale_price >= $package['sale_price'])
                        <button type="submit" class="{{ $package['button'] }} plan-loader flex gap-3">{{ __('Downgrade Plan') }}</button>
                    @endif
                </form>
                <div class="flex flex-col gap-[18px] mt-8">
                    @foreach($package['features'] as $meta)
                        @continue(empty($meta['title']))

                        @if ($meta['is_visible'])
                            <div class="flex items-center text-color-14 dark:text-white text-16 font-medium font-Figtree gap-[9px]">
                                @if($meta['status'] == 'Active')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="11" viewBox="0 0 15 11"
                                        fill="none">
                                        <path
                                            d="M13.88 1.17017C14.2755 1.56567 14.2755 2.20798 13.88 2.60349L5.77995 10.7035C5.38444 11.099 4.74214 11.099 4.34663 10.7035L0.296631 6.65349C-0.0988769 6.25798 -0.0988769 5.61567 0.296631 5.22017C0.692139 4.82466 1.33444 4.82466 1.72995 5.22017L5.06487 8.55192L12.4498 1.17017C12.8453 0.774658 13.4876 0.774658 13.8831 1.17017H13.88Z"
                                            fill="currentColor" />
                                    </svg>
                                @else
                                    <svg width="13" height="14" viewBox="0 0 13 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1.09014 1.59014C1.46032 1.21995 2.06051 1.21995 2.4307 1.59014L6.5 5.65944L10.5693 1.59014C10.9395 1.21995 11.5397 1.21995 11.9099 1.59014C12.28 1.96032 12.28 2.56051 11.9099 2.9307L7.84056 7L11.9099 11.0693C12.28 11.4395 12.28 12.0397 11.9099 12.4099C11.5397 12.78 10.9395 12.78 10.5693 12.4099L6.5 8.34056L2.4307 12.4099C2.06051 12.78 1.46032 12.78 1.09014 12.4099C0.719954 12.0397 0.719954 11.4395 1.09014 11.0693L5.15944 7L1.09014 2.9307C0.719954 2.56051 0.719954 1.96032 1.09014 1.59014Z" fill="#DF2F2F"/>
                                    </svg>
                                @endif

                                @if ($meta['type'] != 'number')
                                    <span class="break-words"> {{ $meta['title'] }} </span>
                                @elseif ($meta['title_position'] == 'before')
                                    <span class="break-words"> {{ $meta['title'] . ': ' }} {{ ($meta['value'] == -1) ? __('Unlimited') : $meta['value'] }} </span>
                                @else
                                    <span class="break-words"> {{ ($meta['value'] == -1 ? __('Unlimited') : $meta['value']) }} {{ $meta['title'] }} </span>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>

            </div>
        </div>
    @endforeach
</div>
