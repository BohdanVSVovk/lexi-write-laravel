<link rel="stylesheet" href="{{ asset('public/assets/css/user/sidebar.min.css') }}">
<div id="overlay" class="fixed z-50 top-0 left-0 bg-darken-4"></div>
@php
    $slug = request('slug') ?? 'demo';
    $color1 = '#141414';
    $color2 = '#141414';
    $menu = activeMenu(route('user.dashboard'));
    $subscription = Modules\Subscription\Entities\PackageSubscription::with(['package'])->where('user_id', Auth::user()->id)->first();

    if($subscription != NULL) {

        $subscriptionMeta = Modules\Subscription\Entities\PackageSubscriptionMeta::where('package_subscription_id', $subscription->id)->where('type','feature_word')->get();
        $creditLimit = $subscriptionMeta->where('key', 'value')->first()->value;
        $creditUsed = $subscriptionMeta->where('key', 'usage')->first()->value;
        $creditPercentage = round( (($creditLimit  - $creditUsed) * 100) / $creditLimit );

    }
@endphp
<nav id="sidenav"
    class="md:pt-14 h-screen sidebar-nav md:sticky z-[100] md:z-50 top-0 left-0 w-[270px] text-color-14 flex flex-col font-Figtree md:transition-all md:duration-500 md:ease-in-out">
    <div class="sidebar-bg-white h-full py-3.5 flex flex-col">
        <div class="sidebar-top relative flex items-center pl-5 dark:border-[#474746] py-3.5 {{ $menu['class'] }} main-menu menus-height">
            <a href="{{ route('user.dashboard') }}" class="flex w-full gap-3 items-center">
                <span class="h-4 w-4 category-svg">
                    <svg width="16" height="17" viewBox="0 0 16 17" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M5.5 13.1664H10.5V16.4998H5.5V13.1664ZM0.5 15.2164L3.83333 11.8831V16.4998H1.33333C0.875 16.4998 0.5 16.1248 0.5 15.6664V15.2164ZM10.3583 2.99976L3.83333 9.52476L0.5 12.8498V8.49143C0.5 8.28309 0.575 8.09143 0.708333 7.94143C0.716667 7.92476 0.733333 7.91643 0.741667 7.89976L7.40833 1.23309C7.43333 1.20809 7.45 1.19143 7.475 1.18309L7.53333 1.12476C7.86667 0.916428 8.30833 0.949761 8.59167 1.23309L10.3583 2.99976ZM15.5 8.49143V15.6664C15.5 16.1248 15.125 16.4998 14.6667 16.4998H12.1667V9.52476L9.18333 6.54143L11.5417 4.18309L15.2583 7.89976C15.4083 8.05809 15.5 8.26643 15.5 8.49143Z"
                        fill="url(#paint0_linear_2671_2007)" />
                    <defs>
                        <linearGradient id="paint0_linear_2671_2007" x1="10.2526" y1="14.5909" x2="4.74869"
                            y2="2.57816" gradientUnits="userSpaceOnUse">
                            <stop stop-color="{{ $menu['color1'] }}" />
                            <stop offset="1" stop-color="{{ $menu['color2'] }}" />
                        </linearGradient>
                    </defs>
                </svg>
                </span>

                <p class="transion-hide text-base leading-[24px] font-normal text-color-14">
                    <span class="dark:text-white">{{ __('Dashboard') }}</span>
                </p>
            </a>
            <span class="shrink-btn absolute top-[50%] opacity-1 right-3.5 cursor-pointer hidden md:block">
                <svg class="dark:hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none">
                    <rect width="24" height="24" rx="4" fill="#141414" />
                    <g clip-path="url(#clip0_344_741)">
                        <path
                            d="M9.95841 7.91675H12.5834L9.66674 12.0001L12.5834 16.0834H9.95841L7.04174 12.0001L9.95841 7.91675Z"
                            fill="white" />
                        <path
                            d="M15.0417 7.91675H17.6667L14.7501 12.0001L17.6667 16.0834H15.0417L12.1251 12.0001L15.0417 7.91675Z"
                            fill="white" />
                    </g>
                    <defs>
                        <clipPath id="clip0_344_741">
                            <rect width="14" height="14" fill="white" transform="matrix(-1 0 0 1 19 5)" />
                        </clipPath>
                    </defs>
                </svg>
                <svg class="hidden dark:block" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none">
                    <rect width="24" height="24" rx="4" fill="white" />
                    <g clip-path="url(#clip0_435_902)">
                        <path
                            d="M9.95841 7.91675H12.5834L9.66674 12.0001L12.5834 16.0834H9.95841L7.04174 12.0001L9.95841 7.91675Z"
                            fill="#141414" />
                        <path
                            d="M15.0417 7.91675H17.6667L14.7501 12.0001L17.6667 16.0834H15.0417L12.1251 12.0001L15.0417 7.91675Z"
                            fill="#141414" />
                    </g>
                    <defs>
                        <clipPath id="clip0_435_902">
                            <rect width="14" height="14" fill="white" transform="matrix(-1 0 0 1 19 5)" />
                        </clipPath>
                    </defs>
                </svg>
            </span>

            <div class="close shrink-btn absolute top-[50%] opacity-1 right-3.5 cursor-pointer md:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none">
                    <rect width="24" height="24" rx="4" fill="#141414" />
                    <g clip-path="url(#clip0_344_741)">
                        <path
                            d="M9.95841 7.91675H12.5834L9.66674 12.0001L12.5834 16.0834H9.95841L7.04174 12.0001L9.95841 7.91675Z"
                            fill="white" />
                        <path
                            d="M15.0417 7.91675H17.6667L14.7501 12.0001L17.6667 16.0834H15.0417L12.1251 12.0001L15.0417 7.91675Z"
                            fill="white" />
                    </g>
                    <defs>
                        <clipPath id="clip0_344_741">
                            <rect width="14" height="14" fill="white" transform="matrix(-1 0 0 1 19 5)" />
                        </clipPath>
                    </defs>
                </svg>
            </div>
        </div>
        <div class="sidebar-links sidebar-accordion middle-sidebar-scroll overflow-y-scroll">
            <ul class="mt-3">
                <li class="w-[52px] div-border border dark:border-[#474746] border-t border-color-DF ml-5 my-3.5">
                </li>
                <li>
                    @php $menu = activeMenu(route('openai'), route('user.template', ['slug' => $slug]) ) @endphp
                    <a href="{{ route('openai') }}">
                        <div
                            class="{{ $menu['class'] }} main-menu flex items-center gap-3 w-full py-3 pl-5 pr-[15px] text-left text-color-14 text-base leading-6 font-normal menus-height">
                            <span class="w-4 h-4">
                                <svg class="category-svg w-4 h-4" width="16" height="16" viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M0.5 8.83333H7.16667V0.5H0.5V8.83333ZM0.5 15.5H7.16667V10.5H0.5V15.5ZM8.83333 15.5H15.5V7.16667H8.83333V15.5ZM8.83333 0.5V5.5H15.5V0.5H8.83333Z"
                                    fill="url(#paint0_linear_3040_2060)" />
                                <defs>
                                    <linearGradient id="paint0_linear_3040_2060" x1="10.2526" y1="13.6538"
                                        x2="5.04573" y2="1.90371" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="{{ $menu['color1'] }}" />
                                        <stop offset="1" stop-color="{{ $menu['color2'] }}" />
                                    </linearGradient>
                                </defs>
                            </svg>
                            </span>
                            

                            <p class="transion-hide accordion-menus"><span
                                    class="dark:text-white">{{ __('Pre-built Templates') }}</span></p>
                        </div>
                    </a>
                </li>
                    @php $menu = activeMenu(route('user.imageTemplate')) @endphp
                    <a href="{{ route('user.imageTemplate') }}">
                        <div
                            class="{{ $menu['class'] }} main-menu flex items-center gap-3 w-full py-3 pl-5 pr-[15px] text-left text-color-14 text-base leading-6 font-normal menus-height">
                            <span class="w-4 h-4">
                                <svg class="category-svg w-4 h-4" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_1997_1547)">
                                    <path
                                        d="M17.5 15.8333V4.16667C17.5 3.25 16.75 2.5 15.8333 2.5H4.16667C3.25 2.5 2.5 3.25 2.5 4.16667V15.8333C2.5 16.75 3.25 17.5 4.16667 17.5H15.8333C16.75 17.5 17.5 16.75 17.5 15.8333ZM7.08333 11.25L9.16667 13.7583L12.0833 10L15.8333 15H4.16667L7.08333 11.25Z"
                                        fill="url(#paint0_linear_1997_1547)" />
                                </g>
                                <defs>
                                    <linearGradient id="paint0_linear_1997_1547" x1="12.2526" y1="15.6538"
                                        x2="7.04573" y2="3.90371" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="{{ $menu['color1'] }}" />
                                        <stop offset="1" stop-color="{{ $menu['color2'] }}" />
                                    </linearGradient>
                                    <clipPath id="clip0_1997_1547">
                                        <rect width="20" height="20" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                            </span>

                            <p class="transion-hide accordion-menus"><span
                                    class="dark:text-white">{{ __('Image Maker') }}</span></p>
                        </div>
                    </a>
                </li>
                <li>
                    @php $menu = activeMenu(route('user.codeTemplate')) @endphp
                    <a href="{{ route('user.codeTemplate') }}">
                        <div
                            class="{{ $menu['class'] }} main-menu flex items-center gap-3 w-full py-3 pl-5 pr-[15px] text-left text-color-14 text-base leading-6 font-normal menus-height">
                            <span class="w-4 h-4">
                                <svg class="category-svg w-4 h-4" width="20" height="16" viewBox="0 0 20 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.8761 7.69945L14.7717 2.59512C14.613 2.43639 14.3322 2.43639 14.1735 2.59512L13.0668 3.70174C12.9016 3.86702 12.9016 4.13495 13.0668 4.30023L16.7655 7.99849L13.067 11.6974C12.9018 11.8627 12.9018 12.1306 13.067 12.2959L14.1737 13.4025C14.253 13.4819 14.3607 13.5267 14.4729 13.5267C14.5849 13.5267 14.6928 13.4819 14.7722 13.4025L19.8761 8.29837C20.0414 8.13287 20.0414 7.86474 19.8761 7.69945Z"
                                        fill="url(#paint0_linear_2838_1839)" />
                                    <path
                                        d="M6.93299 11.6975L3.23494 7.99907L6.93362 4.30081C7.01298 4.22145 7.05764 4.11394 7.05764 4.00156C7.05764 3.8894 7.0132 3.78168 6.93362 3.70232L5.827 2.5957C5.74764 2.51633 5.63992 2.47168 5.52776 2.47168C5.41559 2.47168 5.30787 2.51633 5.22851 2.5957L0.123963 7.69961C-0.041321 7.86489 -0.041321 8.13282 0.123963 8.29831L5.2283 13.4024C5.30766 13.4818 5.41538 13.5267 5.52754 13.5267C5.63971 13.5267 5.74743 13.4818 5.82679 13.4024L6.93341 12.2958C7.09869 12.1307 7.09869 11.8628 6.93299 11.6975Z"
                                        fill="url(#paint1_linear_2838_1839)" />
                                    <path
                                        d="M12.9025 0.877949C12.8488 0.779328 12.7582 0.706104 12.6507 0.674359L11.7536 0.409608C11.5297 0.343156 11.2939 0.471616 11.2279 0.695734L7.0632 14.7995C7.03145 14.9072 7.04373 15.023 7.09727 15.1214C7.15081 15.2202 7.2416 15.2932 7.34911 15.3252L8.24622 15.5899C8.28622 15.6018 8.32664 15.6075 8.36621 15.6075C8.54885 15.6075 8.71752 15.4881 8.77191 15.3038L12.9366 1.19984C12.9683 1.09212 12.9563 0.976357 12.9025 0.877949Z"
                                        fill="url(#paint2_linear_2838_1839)" />
                                    <defs>
                                        <linearGradient id="paint0_linear_2838_1839" x1="17.5312" y1="12.1666"
                                            x2="12.6806" y2="5.17616" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="{{ $menu['color1'] }}" />
                                            <stop offset="1" stop-color="{{ $menu['color2'] }}" />
                                        </linearGradient>
                                        <linearGradient id="paint1_linear_2838_1839" x1="4.58867" y1="12.166"
                                            x2="-0.264378" y2="5.1743" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="{{ $menu['color1'] }}" />
                                            <stop offset="1" stop-color="{{ $menu['color2'] }}" />
                                        </linearGradient>
                                        <linearGradient id="paint2_linear_2838_1839" x1="10.8871" y1="13.7348"
                                            x2="3.81928" y2="7.54154" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="{{ $menu['color1'] }}" />
                                            <stop offset="1" stop-color="{{ $menu['color2'] }}" />
                                        </linearGradient>
                                    </defs>
                                </svg>
                            </span>
                            <p class="transion-hide accordion-menus"><span
                                    class="dark:text-white">{{ __('Code Writer') }}</span></p>
                        </div>
                    </a>
                </li>
                <li class="w-[52px] div-border border dark:border-[#474746] border-t border-color-DF ml-5 my-3.5">
                </li>
                <li>
                    @php $menu = activeMenu(route('user.documents'), route('user.favouriteDocuments'), route('user.editContent', ['slug' => $slug]))  @endphp
                    <a href="{{ route('user.documents')}}">
                        <div
                            class="{{ $menu['class'] }} main-menu flex items-center gap-3 w-full py-3 pl-5 pr-[15px] text-left text-color-14 text-base leading-6 font-normal menus-height">
                            <span class="w-4 h-4">
                                <svg class="category-svg w-4 h-4" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_400_828)">
                                        <path
                                            d="M11.1641 1H5.375C4.16688 1 3.1875 1.97938 3.1875 3.1875V16.3125C3.1875 17.5206 4.16688 18.5 5.375 18.5H14.125C15.3331 18.5 16.3125 17.5206 16.3125 16.3125V6.1484C16.3125 5.85832 16.1973 5.58012 15.9921 5.375L11.9375 1.32035C11.7324 1.11523 11.4542 1 11.1641 1ZM11.3906 4.82812V2.64062L14.6719 5.92188H12.4844C11.8803 5.92188 11.3906 5.43219 11.3906 4.82812ZM5.92188 10.8438C5.61984 10.8438 5.375 10.5989 5.375 10.2969C5.375 9.99484 5.61984 9.75 5.92188 9.75H13.5781C13.8802 9.75 14.125 9.99484 14.125 10.2969C14.125 10.5989 13.8802 10.8438 13.5781 10.8438H5.92188ZM5.375 12.4844C5.375 12.1823 5.61984 11.9375 5.92188 11.9375H13.5781C13.8802 11.9375 14.125 12.1823 14.125 12.4844C14.125 12.7864 13.8802 13.0312 13.5781 13.0312H5.92188C5.61984 13.0312 5.375 12.7864 5.375 12.4844ZM5.92188 15.2188C5.61984 15.2188 5.375 14.9739 5.375 14.6719C5.375 14.3698 5.61984 14.125 5.92188 14.125H10.2969C10.5989 14.125 10.8438 14.3698 10.8438 14.6719C10.8438 14.9739 10.5989 15.2188 10.2969 15.2188H5.92188Z"
                                            fill="url(#paint0_linear_400_828)" />
                                    </g>
                                    <defs>
                                        <linearGradient id="paint0_linear_400_828" x1="11.721" y1="16.3461"
                                            x2="4.53841" y2="4.18957" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="{{ $menu['color1'] }}" />
                                            <stop offset="1" stop-color="{{ $menu['color2'] }}" />
                                        </linearGradient>
                                        <clipPath id="clip0_400_828">
                                            <rect width="20" height="20" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <p class="transion-hide accordion-menus"><span
                                    class="dark:text-white">{{ __('Documents') }}</span></p>
                        </div>
                    </a>
                </li>
                <li>
                    @php $menu = activeMenu(route('user.imageList'), route('user.image.view', ['slug' => $slug]))  @endphp
                    <a href="{{ route('user.imageList') }}">
                        <div
                            class="{{ $menu['class'] }} main-menu flex items-center gap-3 w-full py-3 pl-5 pr-[15px] text-left text-color-14 text-base leading-6 font-normal menus-height">
                            <span class="w-4 h-4">
                                <svg class="category-svg w-4 h-4" width="18" height="18" viewBox="0 0 18 18" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M17.75 9C17.75 11.3206 16.8281 13.5462 15.1872 15.1872C13.5462 16.8281 11.3206 17.75 9 17.75C6.67936 17.75 4.45376 16.8281 2.81282 15.1872C1.17187 13.5462 0.25 11.3206 0.25 9C0.25 8.83424 0.315848 8.67527 0.433058 8.55806C0.550268 8.44085 0.70924 8.375 0.875 8.375C1.04076 8.375 1.19973 8.44085 1.31694 8.55806C1.43415 8.67527 1.5 8.83424 1.5 9C1.4975 10.6738 2.05413 12.3005 3.08154 13.6218C4.10895 14.9432 5.54827 15.8835 7.17105 16.2936C8.79383 16.7036 10.5071 16.5599 12.0389 15.8853C13.5707 15.2106 14.8332 14.0436 15.6262 12.5696C16.4191 11.0955 16.697 9.39886 16.4157 7.74888C16.1344 6.0989 15.31 4.59015 14.0734 3.4621C12.8369 2.33405 11.259 1.65134 9.59018 1.52233C7.92137 1.39332 6.25728 1.82541 4.86204 2.75H5.25C5.41576 2.75 5.57473 2.81585 5.69194 2.93306C5.80915 3.05027 5.875 3.20924 5.875 3.375C5.875 3.54076 5.80915 3.69974 5.69194 3.81695C5.57473 3.93416 5.41576 4 5.25 4H3.375C3.29291 4.00005 3.21162 3.98392 3.13577 3.95252C3.05992 3.92113 2.991 3.8751 2.93295 3.81705C2.87491 3.759 2.82887 3.69009 2.79748 3.61424C2.76609 3.53839 2.74995 3.45709 2.75 3.375V1.5C2.75 1.33424 2.81585 1.17527 2.93306 1.05806C3.05027 0.940853 3.20924 0.875005 3.375 0.875005C3.54076 0.875005 3.69973 0.940853 3.81694 1.05806C3.93415 1.17527 4 1.33424 4 1.5V1.8212C5.31174 0.90694 6.84896 0.369672 8.44463 0.267782C10.0403 0.165893 11.6334 0.50328 13.0507 1.24328C14.4681 1.98328 15.6556 3.09759 16.484 4.46512C17.3125 5.83265 17.7504 7.40109 17.75 9ZM14.625 9C14.625 10.1125 14.2951 11.2001 13.677 12.1251C13.0589 13.0501 12.1804 13.7711 11.1526 14.1968C10.1248 14.6226 8.99376 14.734 7.90262 14.5169C6.81147 14.2999 5.80919 13.7641 5.02252 12.9775C4.23585 12.1908 3.70012 11.1885 3.48308 10.0974C3.26604 9.00624 3.37743 7.87524 3.80318 6.84741C4.22892 5.81958 4.94989 4.94107 5.87492 4.32299C6.79994 3.7049 7.88748 3.375 9 3.375C10.4913 3.37663 11.9211 3.96979 12.9757 5.02433C14.0302 6.07887 14.6234 7.50866 14.625 9ZM11.2217 9.73L9.625 8.66553V5.875C9.625 5.70924 9.55915 5.55027 9.44194 5.43306C9.32473 5.31585 9.16576 5.25 9 5.25C8.83424 5.25 8.67527 5.31585 8.55806 5.43306C8.44085 5.55027 8.375 5.70924 8.375 5.875V9C8.37502 9.10289 8.40043 9.20418 8.44898 9.29489C8.49753 9.3856 8.56772 9.46292 8.65332 9.52L10.5283 10.77C10.6662 10.86 10.8341 10.892 10.9955 10.8589C11.1568 10.8258 11.2986 10.7304 11.39 10.5933C11.4813 10.4563 11.5149 10.2887 11.4834 10.1271C11.4519 9.96539 11.3578 9.8227 11.2217 9.73Z"
                                    fill="url(#paint0_linear_3321_1869)" />
                                <defs>
                                    <linearGradient id="paint0_linear_3321_1869" x1="11.628" y1="15.5961"
                                        x2="5.55335" y2="1.88766" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="{{ $menu['color1'] }}" />
                                        <stop offset="1" stop-color="{{ $menu['color2'] }}" />
                                    </linearGradient>
                                </defs>
                            </svg>
                            </span>
                            <p class="transion-hide accordion-menus"><span
                                    class="dark:text-white">{{ __('Image History') }}</span></p>
                        </div>
                    </a>
                </li>
                <li>
                    @php $menu = activeMenu(route('user.codeList'), route('user.codeView', ['slug' => $slug]))  @endphp
                    <a href="{{ route('user.codeList') }}">
                        <div
                            class="{{ $menu['class'] }} main-menu flex items-center gap-3 w-full py-3 pl-5 pr-[15px] text-left text-color-14 text-base leading-6 font-normal menus-height">
                            <span class="w-4 h-4">
                                <svg class="category-svg w-4 h-4" width="14" height="18" viewBox="0 0 14 18" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M8.25 3.375V0.25H2C1.66848 0.25 1.35054 0.381696 1.11612 0.616116C0.881696 0.850537 0.75 1.16848 0.75 1.5V16.5C0.75 16.8315 0.881696 17.1495 1.11612 17.3839C1.35054 17.6183 1.66848 17.75 2 17.75H12C12.3315 17.75 12.6495 17.6183 12.8839 17.3839C13.1183 17.1495 13.25 16.8315 13.25 16.5V5.25H10.125C9.62772 5.25 9.15081 5.05246 8.79917 4.70083C8.44754 4.34919 8.25 3.87228 8.25 3.375ZM4.94375 11.6813C5.00233 11.7394 5.04883 11.8085 5.08056 11.8846C5.11229 11.9608 5.12862 12.0425 5.12862 12.125C5.12862 12.2075 5.11229 12.2892 5.08056 12.3654C5.04883 12.4415 5.00233 12.5106 4.94375 12.5687C4.88565 12.6273 4.81652 12.6738 4.74036 12.7056C4.6642 12.7373 4.58251 12.7536 4.5 12.7536C4.41749 12.7536 4.3358 12.7373 4.25964 12.7056C4.18348 12.6738 4.11435 12.6273 4.05625 12.5687L2.80625 11.3187C2.74767 11.2606 2.70117 11.1915 2.66944 11.1154C2.63771 11.0392 2.62138 10.9575 2.62138 10.875C2.62138 10.7925 2.63771 10.7108 2.66944 10.6346C2.70117 10.5585 2.74767 10.4894 2.80625 10.4313L4.05625 9.18125C4.17394 9.06356 4.33356 8.99744 4.5 8.99744C4.66644 8.99744 4.82606 9.06356 4.94375 9.18125C5.06144 9.29894 5.12756 9.45856 5.12756 9.625C5.12756 9.79144 5.06144 9.95106 4.94375 10.0687L4.13125 10.875L4.94375 11.6813ZM8.21875 9.18125L6.96875 12.9313C6.93008 13.0589 6.85159 13.1708 6.74476 13.2506C6.63793 13.3305 6.50836 13.3741 6.375 13.375C6.30726 13.3733 6.24003 13.3628 6.175 13.3438C6.09691 13.3177 6.02474 13.2764 5.96265 13.2223C5.90055 13.1683 5.84975 13.1025 5.81316 13.0287C5.77658 12.9549 5.75492 12.8747 5.74944 12.7925C5.74397 12.7104 5.75478 12.628 5.78125 12.55L7.03125 8.8C7.08429 8.64253 7.19772 8.51258 7.34658 8.43873C7.49543 8.36489 7.66753 8.35321 7.825 8.40625C7.98247 8.45929 8.11242 8.57272 8.18627 8.72158C8.26011 8.87043 8.27179 9.04253 8.21875 9.2V9.18125ZM11.1937 11.3L9.94375 12.55C9.88565 12.6086 9.81652 12.6551 9.74036 12.6868C9.6642 12.7185 9.58251 12.7349 9.5 12.7349C9.41749 12.7349 9.3358 12.7185 9.25964 12.6868C9.18348 12.6551 9.11435 12.6086 9.05625 12.55C8.99767 12.4919 8.95117 12.4228 8.91944 12.3466C8.88771 12.2704 8.87138 12.1888 8.87138 12.1062C8.87138 12.0237 8.88771 11.9421 8.91944 11.8659C8.95117 11.7897 8.99767 11.7206 9.05625 11.6625L9.86875 10.875L9.05625 10.0687C8.93856 9.95106 8.87244 9.79144 8.87244 9.625C8.87244 9.45856 8.93856 9.29894 9.05625 9.18125C9.17394 9.06356 9.33356 8.99744 9.5 8.99744C9.66644 8.99744 9.82606 9.06356 9.94375 9.18125L11.1937 10.4313C11.2523 10.4894 11.2988 10.5585 11.3306 10.6346C11.3623 10.7108 11.3786 10.7925 11.3786 10.875C11.3786 10.9575 11.3623 11.0392 11.3306 11.1154C11.2988 11.1915 11.2523 11.2606 11.1937 11.3187V11.3Z"
                                    fill="url(#paint0_linear_3338_1879)" />
                                <defs>
                                    <linearGradient id="paint0_linear_3338_1879" x1="8.87714" y1="15.5961"
                                        x2="1.53029" y2="3.75367" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="{{ $menu['color1'] }}" />
                                        <stop offset="1" stop-color="{{ $menu['color2'] }}" />
                                    </linearGradient>
                                </defs>
                            </svg>
                            </span>
                            <p class="transion-hide accordion-menus"><span
                                    class="dark:text-white">{{ __('Code History') }}</span></p>
                        </div>
                    </a>
                </li>
                <li class="w-[52px] div-border border dark:border-[#474746] border-t border-color-DF ml-5 my-3.5">
                </li>
                <li>
                    @php $menu = activeMenu(route('user.profile'), route('user.package'), route('user.subscription.history'))  @endphp
                    <a href="{{ route('user.profile') }}">
                        <div
                            class="{{ $menu['class'] }} main-menu flex items-center gap-3 w-full py-3 pl-5 pr-[15px] text-left text-color-14 text-base leading-6 font-normal menus-height">
                            <span class="w-4 h-4">
                                <svg class="category-svg w-4 h-4" width="18" height="18" viewBox="0 0 18 18" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M8.99984 0.666626C4.39984 0.666626 0.666504 4.39996 0.666504 8.99996C0.666504 13.6 4.39984 17.3333 8.99984 17.3333C13.5998 17.3333 17.3332 13.6 17.3332 8.99996C17.3332 4.39996 13.5998 0.666626 8.99984 0.666626ZM8.99984 3.16663C10.3832 3.16663 11.4998 4.28329 11.4998 5.66663C11.4998 7.04996 10.3832 8.16663 8.99984 8.16663C7.6165 8.16663 6.49984 7.04996 6.49984 5.66663C6.49984 4.28329 7.6165 3.16663 8.99984 3.16663ZM8.99984 15C6.9165 15 5.07484 13.9333 3.99984 12.3166C4.02484 10.6583 7.33317 9.74996 8.99984 9.74996C10.6582 9.74996 13.9748 10.6583 13.9998 12.3166C12.9248 13.9333 11.0832 15 8.99984 15Z"
                                    fill="url(#paint0_linear_460_1019)" />
                                <defs>
                                    <linearGradient id="paint0_linear_460_1019" x1="11.5027" y1="15.2819"
                                        x2="5.71732" y2="2.2263" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="{{ $menu['color1'] }}" />
                                        <stop offset="1" stop-color="{{ $menu['color2'] }}" />
                                    </linearGradient>
                                </defs>
                            </svg>
                            </span>
                            <p class="transion-hide accordion-menus"><span
                                    class="dark:text-white">{{ __('Account') }}</span></p>
                        </div>
                    </a>
                </li>
            </ul>
            @if($subscription != NULL && in_array($subscription->status, ['Active', 'Cancel']))
            <div class="bg-color-F6 dark:bg-[#434241] border border-color-DF dark:border-color-47 rounded-xl p-4 mx-5 mt-3 mb-7 plan-card">
                <div class="flex justify-start items-cetner gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <g clip-path="url(#clip0_5419_1957)">
                        <path d="M12.419 6.22813C12.327 6.08513 12.169 6.00014 12 6.00014H8.50006V0.500211C8.50006 0.264215 8.33506 0.0602174 8.10407 0.0112181C7.86907 -0.0387812 7.63907 0.0822171 7.54307 0.297214L3.54313 9.29709C3.47413 9.45109 3.48913 9.63109 3.58113 9.77208C3.67313 9.91408 3.83112 10.0001 4.00012 10.0001H7.50007V15.5C7.50007 15.736 7.66507 15.94 7.89607 15.989C7.93107 15.996 7.96607 16 8.00007 16C8.19407 16 8.37506 15.887 8.45706 15.703L12.457 6.70313C12.525 6.54813 12.512 6.37013 12.419 6.22813Z" fill="url(#paint0_linear_5419_1957)"/>
                        </g>
                        <defs>
                        <linearGradient id="paint0_linear_5419_1957" x1="9.35152" y1="14.0307" x2="2.06253" y2="4.77849" gradientUnits="userSpaceOnUse">
                        <stop offset="0" stop-color="#E60C84"/>
                        <stop offset="1" stop-color="#FFCF4B"/>
                        </linearGradient>
                        <clipPath id="clip0_5419_1957">
                        <rect width="16" height="16" fill="white"/>
                        </clipPath>
                        </defs>
                    </svg>
                    <p class="text-color-14 dark:text-white text-sm font-semibold font-Figtree">{{ optional($subscription->package)->name }}</p>
                </div>
                <p class="text-color-14 dark:text-white font-Figtree font-normal text-sm mt-2.5">
                    {!! __('You have :x words left in your :y plan', [ 'x' =>  '<span class="total-word-used text-[#E22861] dark:text-[#FCCA19]">' . ($creditUsed > $creditLimit ? ($creditLimit == -1 ? $creditUsed : $creditLimit) : $creditUsed) . '</span>' .  '<span class="credit-limit text-[#E22861] dark:text-[#FCCA19]">/' . ($creditLimit == -1 ? __('Unlimited') : $creditLimit) . '</span>', 'y' => ($subscription->billing_cycle == 'days' ? $subscription->duration . ' ' : '') . $subscription->billing_cycle ]) !!}
                </p>
                <div
                    class="relative h-1 w-full bg-white dark:bg-color-3A rounded-[25px] border border-color-DF dark:border-color-47 mt-3">
                    <div
                        class="progress-fill absolute h-1 rounded-[60px] w-[30%]" style="width: {{ ($creditLimit == -1) ? 0 : ((100 - $creditPercentage) > 100 ? 100 : (100 - $creditPercentage)) }}%">
                    </div>
                </div>
                <a
                class="magic-bg rounded-xl text-[13px] text-white justify-center items-center font-semibold py-2 w-full mx-auto flex text-center mt-4 cursor-pointer font-Figtree" href="{{ route('frontend.pricing') }}">
                    <span>
                        {{ __('Upgrade') }}
                    </span>
                </a>
            </div>
        @endif
        </div>
        <div class="sidebar-footer relative mt-auto">
            <div class="w-[52px] div-border border dark:border-[#474746] border-t border-color-DF ml-5 my-3.5">
            </div>
            <div class="flex items-center h-[52px] justify-start pl-5 w-full bottom-0">
                <label for="switch" class="flex items-center cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" id="switch" class="sr-only" {{ \Cookie::get('theme_preference') == 'dark' ? 'checked' : '' }} >
                        <div
                            class="block bg-color-DF dark:bg-[#FF774B] border border-color-89 dark:border-[#FF774B] w-9 h-5 rounded-full">
                        </div>
                        <div class="dot absolute left-[2px] top-[2px] bg-white w-4 h-4 rounded-full transition"></div>
                    </div>
                    <div class="ml-3 transion-hide text-color-14 font-normal text-base leading-6">
                        <span class="dark:text-[#333332] dark:hidden">{{ __('Dark Mode') }}</span>
                        <span class="dark:text-white text-white dark:flex hidden">{{ __('Light Mode') }}</span>
                    </div>
                </label>
            </div>
        </div>
    </div>
</nav>
