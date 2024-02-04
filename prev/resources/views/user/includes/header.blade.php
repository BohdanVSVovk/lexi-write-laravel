<header>
    <div class="fixed top-0 right-0 left-0 w-full font-Figtree z-[99]">
        <nav
            class="flex h-[56px] items-center justify-between bg-color-2C transition-all duration-500 ease-in-out dark:bg-[#141414]">
            <div class="w-full h-[56px] flex items-center justify-between md:mx-5 gap-3">

                <div class="flex items-center justify-center">
                    <div class="w-[66px] h-[56px] mr-4 bg-[#464444] flex items-center justify-center md:hidden">
                        <svg class="collapse-icon pointer" width="26" height="26" viewBox="0 0 26 26" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 13H22M4 7H22M4 19H16" stroke="white" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                        </svg>
                    </div>

                    <a href="{{ route('user.dashboard') }}" class="b-brand">
                        @php
                            $logo = App\Models\Preference::getLogo('company_logo');
                        @endphp
                        <img width="104" height="28" src="{{ $logo }}" alt="{{ trimWords(preference('company_name'), 17)}}">
                    </a>
                </div>

                <div>
                    <a href="{{ route('users.logout') }}"
                        class="flex justify-center gap-2 items-center text-white hover:text-color-FC mr-5 md:mr-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                            fill="none">
                            <g clip-path="url(#clip0_269_443)">
                                <path
                                    d="M11.3334 4.66667L10.3934 5.60667L12.1134 7.33333H5.33337V8.66667H12.1134L10.3934 10.3867L11.3334 11.3333L14.6667 8L11.3334 4.66667ZM2.66671 3.33333H8.00004V2H2.66671C1.93337 2 1.33337 2.6 1.33337 3.33333V12.6667C1.33337 13.4 1.93337 14 2.66671 14H8.00004V12.6667H2.66671V3.33333Z"
                                    fill="currentColor"></path>
                            </g>
                            <defs>
                                <clipPath id="clip0_269_443">
                                    <rect width="16" height="16" fill="white"></rect>
                                </clipPath>
                            </defs>
                        </svg>
                        <span class="font-normal text-[15px] leading-[22px] line-clamp-single">{{ __('Logout') }}</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>
</header>
