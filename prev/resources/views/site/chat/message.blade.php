@php
    $contacts = [];
    $messageCount = 0;
    if (!empty(Auth::user()->id)) {
        $contacts = Modules\OpenAI\Services\ChatService::getMyContactListWithLastMessage();
        $bot = Modules\OpenAI\Services\ChatService::getBotName();
    }

@endphp

<div class="chat-parent-container">
    <div id="user_image" class="hidden" data-image="{{ auth()->user()?->fileUrl() }}"></div>
    <div class="chat-toggle-container">
        <div class="chat-toggle-button">
            <img src="{{ asset('Modules/OpenAI/Resources/assets/image/chat-robot.png') }}" alt="chat-robot">
        </div>
    </div>
    <div class="chat-view-container chat-hidden">
        <div class="chat-view-header">
            <div class="chat-view-header-text">
                @if(Auth()->user())
                    <div class="chat-sidebar-user-img">
                        <img class="chat-sidebar-img" src="{{ $bot->fileUrl() }}">
                    </div>
                @else
                <div class="chat-message-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 19" fill="none">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.94606 4.0182e-07H10.0539C11.4126 -1.49762e-05 12.5083 -2.74926e-05 13.3874 0.0894005C14.2948 0.181709 15.0817 0.377504 15.7779 0.842653C16.3238 1.20745 16.7926 1.6762 17.1573 2.22215C17.6225 2.91829 17.8183 3.70523 17.9106 4.61264C18 5.49173 18 6.58738 18 7.94604V8.05396C18 9.41262 18 10.5083 17.9106 11.3874C17.8183 12.2948 17.6225 13.0817 17.1573 13.7778C16.7926 14.3238 16.3238 14.7926 15.7778 15.1573C15.1699 15.5636 14.4931 15.7642 13.7267 15.8701C13.1247 15.9534 12.4279 15.9827 11.6213 15.9935L10.7889 17.6584C10.0518 19.1325 7.94819 19.1325 7.21115 17.6584L6.37872 15.9935C5.57211 15.9827 4.87525 15.9534 4.2733 15.8701C3.50685 15.7642 2.83014 15.5636 2.22215 15.1573C1.6762 14.7926 1.20745 14.3238 0.842653 13.7778C0.377504 13.0817 0.181709 12.2948 0.0894005 11.3874C-2.74926e-05 10.5083 -1.49762e-05 9.41261 4.0182e-07 8.05394V7.94606C-1.49762e-05 6.58739 -2.74926e-05 5.49174 0.0894005 4.61264C0.181709 3.70523 0.377504 2.91829 0.842653 2.22215C1.20745 1.6762 1.6762 1.20745 2.22215 0.842653C2.91829 0.377504 3.70523 0.181709 4.61264 0.0894005C5.49174 -2.74926e-05 6.58739 -1.49762e-05 7.94606 4.0182e-07ZM4.81505 2.07913C4.06578 2.15535 3.64604 2.29662 3.33329 2.50559C3.00572 2.72447 2.72447 3.00572 2.50559 3.33329C2.29662 3.64604 2.15535 4.06578 2.07913 4.81505C2.00121 5.58104 2 6.57472 2 8C2 9.42527 2.00121 10.419 2.07913 11.1849C2.15535 11.9342 2.29662 12.354 2.50559 12.6667C2.72447 12.9943 3.00572 13.2755 3.33329 13.4944C3.60665 13.6771 3.96223 13.8081 4.54716 13.889C5.14815 13.9721 5.92075 13.9939 7.00436 13.9986C7.40885 14.0004 7.75638 14.2421 7.91233 14.5886L9 16.7639L10.0877 14.5886C10.2436 14.2421 10.5912 14.0004 10.9956 13.9986C12.0792 13.9939 12.8518 13.9721 13.4528 13.889C14.0378 13.8081 14.3933 13.6771 14.6667 13.4944C14.9943 13.2755 15.2755 12.9943 15.4944 12.6667C15.7034 12.354 15.8446 11.9342 15.9209 11.1849C15.9988 10.419 16 9.42527 16 8C16 6.57472 15.9988 5.58104 15.9209 4.81505C15.8446 4.06578 15.7034 3.64604 15.4944 3.33329C15.2755 3.00572 14.9943 2.72447 14.6667 2.50559C14.354 2.29662 13.9342 2.15535 13.1849 2.07913C12.419 2.00121 11.4253 2 10 2H8C6.57473 2 5.58104 2.00121 4.81505 2.07913Z" fill="currentColor"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5 6C5 5.44772 5.44772 5 6 5L12 5C12.5523 5 13 5.44772 13 6C13 6.55228 12.5523 7 12 7L6 7C5.44772 7 5 6.55228 5 6Z" fill="currentColor"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5 10C5 9.44772 5.44772 9 6 9H9C9.55228 9 10 9.44772 10 10C10 10.5523 9.55228 11 9 11H6C5.44772 11 5 10.5523 5 10Z" fill="currentColor"></path>
                    </svg>
                </div>
                @endif
                <span class="text-white {{ Auth()->user()? 'ml-2':'' }}"> {{ Auth()->user() ? $bot->name : __('Messages') }} </span>
            </div>
            <div class="chat-view-close-button">
                <span class="chat-message-icon m-0 neg-transition-scale">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0.402728 0.402728C0.939699 -0.134243 1.8103 -0.134243 2.34727 0.402728L10.5973 8.65273C11.1342 9.1897 11.1342 10.0603 10.5973 10.5973C10.0603 11.1342 9.1897 11.1342 8.65273 10.5973L0.402728 2.34727C-0.134243 1.8103 -0.134243 0.939699 0.402728 0.402728Z" fill="#fff"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M10.5973 0.402728C10.0603 -0.134243 9.1897 -0.134243 8.65273 0.402728L0.402728 8.65273C-0.134243 9.1897 -0.134243 10.0603 0.402728 10.5973C0.939699 11.1342 1.8103 11.1342 2.34727 10.5973L10.5973 2.34727C11.1342 1.8103 11.1342 0.939699 10.5973 0.402728Z" fill="#fff"></path>
                    </svg>
                </span>
            </div>
        </div>
        @auth
        <div class="chat-view-body">
            <div class="flex flex-col  bg-[#2c2c2c]">
                <div class="chat-view-sidebar chat-header-sidebar-mobile relative">
                    <div>
                        <div class="chat-sidebar-users p-2 flex flex-col gap-2" id ="user-img" data-url ="{{ Auth()->user() ? Auth()->user()->fileUrl() : '' }}">
                            @foreach($contacts as $contact)
                            <div class="chat-sidebar-user border border-[#898989] rounded chat-list list-{{ $contact->chat_conversation_id }}" id="{{ $contact->chat_conversation_id }}">
                                <div>
                                    <div class="flex justify-between relative title-container">
                                        <p class="editable-title text-white text-left text-[13px]">{{ trimWords(optional($contact->chatConversation)->title, 20) }}</p>
                                        <div class="flex gap-2">
                                            <a class="text-white justify-center chat-modal opacity-0" href="javascript: void(0)" id="{{ $contact->chat_conversation_id }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                    <path d="M3.84615 2.8C3.37884 2.8 3 3.15817 3 3.6V4.4C3 4.84183 3.37884 5.2 3.84615 5.2H4.26923V12.4C4.26923 13.2837 5.0269 14 5.96154 14H11.0385C11.9731 14 12.7308 13.2837 12.7308 12.4V5.2H13.1538C13.6212 5.2 14 4.84183 14 4.4V3.6C14 3.15817 13.6212 2.8 13.1538 2.8H10.1923C10.1923 2.35817 9.81347 2 9.34615 2H7.65385C7.18653 2 6.80769 2.35817 6.80769 2.8H3.84615ZM6.38462 6C6.61827 6 6.80769 6.17909 6.80769 6.4V12C6.80769 12.2209 6.61827 12.4 6.38462 12.4C6.15096 12.4 5.96154 12.2209 5.96154 12L5.96154 6.4C5.96154 6.17909 6.15096 6 6.38462 6ZM8.5 6C8.73366 6 8.92308 6.17909 8.92308 6.4V12C8.92308 12.2209 8.73366 12.4 8.5 12.4C8.26634 12.4 8.07692 12.2209 8.07692 12V6.4C8.07692 6.17909 8.26634 6 8.5 6ZM11.0385 6.4V12C11.0385 12.2209 10.849 12.4 10.6154 12.4C10.3817 12.4 10.1923 12.2209 10.1923 12V6.4C10.1923 6.17909 10.3817 6 10.6154 6C10.849 6 11.0385 6.17909 11.0385 6.4Z" fill="currentColor"></path>
                                                </svg>
                                            </a>
                                            <a class="edit-icon text-white justify-center opacity-0 w-4" id="{{ $contact->chat_conversation_id }}" href="javascript: void(0)">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M2.73266 10.0443L2.01789 13.1291C1.99323 13.2419 1.99407 13.3587 2.02036 13.4711C2.04665 13.5835 2.09771 13.6886 2.16982 13.7787C2.24193 13.8689 2.33326 13.9418 2.43715 13.9921C2.54104 14.0424 2.65485 14.0689 2.77028 14.0696C2.82407 14.075 2.87826 14.075 2.93205 14.0696L6.03568 13.3548L11.9947 7.41841L8.66906 4.10034L2.73266 10.0443Z" fill="currentColor"/>
                                                    <path d="M13.8682 4.44626L11.6486 2.22669C11.5027 2.0815 11.3052 2 11.0993 2C10.8935 2 10.696 2.0815 10.5501 2.22669L9.31616 3.46062L12.638 6.78245L13.8719 5.54852C13.9441 5.47594 14.0013 5.38984 14.0402 5.29514C14.0791 5.20043 14.099 5.09899 14.0986 4.99661C14.0983 4.89423 14.0777 4.79292 14.0382 4.69849C13.9986 4.60405 13.9409 4.51834 13.8682 4.44626Z" fill="currentColor"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <a class="text-[14px] text-white cursor-pointer new-chat bg-[#3d3d3d] rounded-t-xl py-2 w-full border border-t border-[#898989]">
                   {{ __('New Chat') }}
                </a>
            </div>
            <div class="relative chat-view-inbox dark:bg-[#383837]">
                <div class="chat-inbox-loader-overlay hidden">
                    <span class="loader-container">
                        <span class="icon-spinner"></span>
                    </span>
                </div>
                <div class="chat-inbox-body">
                    <ul class="chat-inbox-message-list">
                        <li class="chat-inbox-single-item chat-inbox-sent ">

                        </li>
                        <li class="chat-inbox-single-item chat-inbox-received">

                        </li>
                    </ul>
                    <div class="chat-bubble hidden">
                        <div class="typing">
                          <div class="dot"></div>
                          <div class="dot"></div>
                          <div class="dot"></div>
                        </div>
                    </div>
                    <input type="hidden" id="messageId" value="">
                </div>
                <form class="chat-inbox-footer" id="message-form" method="post" enctype="multipart/form-data">
                    <div class="chat-error-message">
                        <ul>
                        </ul>
                    </div>
                    <div class="chat-inbox-input-group">
                        <div class="chat-inbox-text-input">
                            <textarea class="sidebar-scrollbar" name="inbox_message" id="message-to-send" placeholder='{{ __("Type your message..") }}' rows="1" spellcheck="false"></textarea>
                        </div>
                    </div>
                    <div class="chat-inbox-send-button">
                        <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g filter="url(#filter0_d_4393_3469)">
                                <circle cx="40" cy="28" r="20" fill="url(#paint0_linear_4393_3469)" />
                            </g>
                            <path d="M34.1489 25.1492L35.5395 27.7123C35.748 28.0967 35.8523 28.2889 35.8523 28.5C35.8523 28.7111 35.748 28.9033 35.5395 29.2877L34.1489 31.8508C33.2265 33.5509 32.7653 34.401 33.1191 34.8255C33.4728 35.25 34.3179 34.8607 36.008 34.0819L36.008 34.0819L44.9892 29.9433C46.3297 29.3256 47 29.0168 47 28.5C47 27.9832 46.3297 27.6744 44.9892 27.0567L36.008 22.9181C34.3179 22.1393 33.4728 21.75 33.1191 22.1745C32.7653 22.599 33.2265 23.4491 34.1489 25.1492Z" fill="white" />
                            <defs>
                                <filter id="filter0_d_4393_3469" x="0" y="0" width="80" height="80" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                    <feMorphology radius="15" operator="erode" in="SourceAlpha" result="effect1_dropShadow_4393_3469" />
                                    <feOffset dy="12" />
                                    <feGaussianBlur stdDeviation="17.5" />
                                    <feComposite in2="hardAlpha" operator="out" />
                                    <feColorMatrix type="matrix" values="0 0 0 0 0.462745 0 0 0 0 0.235294 0 0 0 0 0.831373 0 0 0 1 0" />
                                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_4393_3469" />
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_4393_3469" result="shape" />
                                </filter>
                                <linearGradient id="paint0_linear_4393_3469" x1="46.0069" y1="43.0768" x2="32.122" y2="11.7432" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" stop-color="#E60C84" />
                                    <stop offset="1" stop-color="#FFCF4B" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </form>
            </div>
        </div>
        @else
        <div class="no-msg-container chat-history">
            <div class="login-chat-message-design">
                <svg class="msg-header-icon text-[#2c2c2c] dark:text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 19" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.94606 4.0182e-07H10.0539C11.4126 -1.49762e-05 12.5083 -2.74926e-05 13.3874 0.0894005C14.2948 0.181709 15.0817 0.377504 15.7779 0.842653C16.3238 1.20745 16.7926 1.6762 17.1573 2.22215C17.6225 2.91829 17.8183 3.70523 17.9106 4.61264C18 5.49173 18 6.58738 18 7.94604V8.05396C18 9.41262 18 10.5083 17.9106 11.3874C17.8183 12.2948 17.6225 13.0817 17.1573 13.7778C16.7926 14.3238 16.3238 14.7926 15.7778 15.1573C15.1699 15.5636 14.4931 15.7642 13.7267 15.8701C13.1247 15.9534 12.4279 15.9827 11.6213 15.9935L10.7889 17.6584C10.0518 19.1325 7.94819 19.1325 7.21115 17.6584L6.37872 15.9935C5.57211 15.9827 4.87525 15.9534 4.2733 15.8701C3.50685 15.7642 2.83014 15.5636 2.22215 15.1573C1.6762 14.7926 1.20745 14.3238 0.842653 13.7778C0.377504 13.0817 0.181709 12.2948 0.0894005 11.3874C-2.74926e-05 10.5083 -1.49762e-05 9.41261 4.0182e-07 8.05394V7.94606C-1.49762e-05 6.58739 -2.74926e-05 5.49174 0.0894005 4.61264C0.181709 3.70523 0.377504 2.91829 0.842653 2.22215C1.20745 1.6762 1.6762 1.20745 2.22215 0.842653C2.91829 0.377504 3.70523 0.181709 4.61264 0.0894005C5.49174 -2.74926e-05 6.58739 -1.49762e-05 7.94606 4.0182e-07ZM4.81505 2.07913C4.06578 2.15535 3.64604 2.29662 3.33329 2.50559C3.00572 2.72447 2.72447 3.00572 2.50559 3.33329C2.29662 3.64604 2.15535 4.06578 2.07913 4.81505C2.00121 5.58104 2 6.57472 2 8C2 9.42527 2.00121 10.419 2.07913 11.1849C2.15535 11.9342 2.29662 12.354 2.50559 12.6667C2.72447 12.9943 3.00572 13.2755 3.33329 13.4944C3.60665 13.6771 3.96223 13.8081 4.54716 13.889C5.14815 13.9721 5.92075 13.9939 7.00436 13.9986C7.40885 14.0004 7.75638 14.2421 7.91233 14.5886L9 16.7639L10.0877 14.5886C10.2436 14.2421 10.5912 14.0004 10.9956 13.9986C12.0792 13.9939 12.8518 13.9721 13.4528 13.889C14.0378 13.8081 14.3933 13.6771 14.6667 13.4944C14.9943 13.2755 15.2755 12.9943 15.4944 12.6667C15.7034 12.354 15.8446 11.9342 15.9209 11.1849C15.9988 10.419 16 9.42527 16 8C16 6.57472 15.9988 5.58104 15.9209 4.81505C15.8446 4.06578 15.7034 3.64604 15.4944 3.33329C15.2755 3.00572 14.9943 2.72447 14.6667 2.50559C14.354 2.29662 13.9342 2.15535 13.1849 2.07913C12.419 2.00121 11.4253 2 10 2H8C6.57473 2 5.58104 2.00121 4.81505 2.07913Z" fill="currentColor" />
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5 6C5 5.44772 5.44772 5 6 5L12 5C12.5523 5 13 5.44772 13 6C13 6.55228 12.5523 7 12 7L6 7C5.44772 7 5 6.55228 5 6Z" fill="currentColor" />
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5 10C5 9.44772 5.44772 9 6 9H9C9.55228 9 10 9.44772 10 10C10 10.5523 9.55228 11 9 11H6C5.44772 11 5 10.5523 5 10Z" fill="currentColor" />
                </svg>
                <p class="new-conv ml-3">
                    {!! __('You need to :x to initiate chat.', ['x' => '<a class="underline font-semibold" href=' . route('login')  . '>' . __('login') . '</a>']) !!}
                </p>
            </div>
        </div>
        @endauth
    </div>
</div>
<script>
    var botImage = '{{ !empty(Auth::user()->id) ? str_replace("\\", "/", $bot->fileUrl()) : "" }}';
</script>
