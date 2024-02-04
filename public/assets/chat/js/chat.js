"use strict";

    var toastMixin = Swal.mixin({
        toast: true,
        icon: "error",
        title: "General Title",
        animation: false,
        position: "top",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: false,
        didOpen: (toast) => {
            toast.addEventListener("mouseenter", Swal.stopTimer);
            toast.addEventListener("mouseleave", Swal.resumeTimer);
        },
    });

    /**
     * Toggles chat container
     */
    $(document).on("click", ".chat-toggle-button", function () {
        if($('div').hasClass("chat-sidebar-user")) {
            var ID = $('.chat-sidebar-user').first().attr('id');
            fetchData(ID)
        }

        $(this).toggleClass("chat-hidden");
        $(".chat-view-container").toggleClass("chat-hidden");
    });

    /**
     * CLoses chat container
     */
    $(document).on("click", ".chat-view-close-button", function () {
        $("#message-to-send").trigger("focus");
        $(".chat-toggle-button").trigger("click");
    });

    $(document).on("click", ".dashboard-chat", function () {
        $(".chat-toggle-button").trigger("click");
    });



    $('#message-to-send').on("keyup", function (e) {
        if (e.keyCode === 13 && !e.shiftKey) {
            $('.chat-inbox-send-button').trigger('click');
        }
    });

    $(document).on('click', '.new-chat', function(){
        let assistantImage = $('.active-assistant').attr('data-image');
        let assistantMessage = $('.active-assistant').attr('data-message');
        $('.chat-inbox-message-list').html(`
        <li class="chat-inbox-single-item chat-inbox-received">
            <div class="chat-inbox-single-avatar">
                <img src="${assistantImage}" alt="chat-robot">
            </div>
            <div>
            <div class="chat-inbox-single-content">
                <code class="font-Figtree whitespace-pre-wrap">${filterXSS(assistantMessage)}</code>
            </div>
            </div>
        </li>`
        );
        $('#messageId').val('')
        $("#message-to-send").trigger("focus");
    });

    function fetchData(id)
    {
        $.ajax({
            url: SITE_URL + '/' + 'user/chat-history/' + id,
            type: "get",
            beforeSend: function (xhr) {
                $('.chat-inbox-message-list').html('');
                $('.chat-inbox-loader-overlay').show();
                $(".chat-sidebar-user").removeClass("chat-user-active");
                $('.list-'+ id).addClass('chat-user-active');
            },
            data: {
                id: id,
            },
            success: function(response) {
                if (response.error) {
                    errorMessage(response.error.message);
                    return true;
                }
                appendData(response, id);
            },

            error: function(response) {
                var jsonData = JSON.parse(response.responseText);
                errorMessage(jsonData.response.status.message, 'code-creation');
             }
        });
    }

    function appendData(response, id) {
        let html = '';
        let userImage = $('#user-img').attr('data-url');
        let assistantImage = $('.active-assistant').attr('data-image');
        let assistantMessage = $('.active-assistant').attr('data-message');

        var totalItem = response.length;
        html += `<li class="chat-inbox-single-item chat-inbox-received">
                    <div class="chat-inbox-single-avatar">
                        <img src="${assistantImage}" alt="chat-robot">
                    </div>
                    <div>
                    <div class="chat-inbox-single-content">
                        <code class="font-Figtree whitespace-pre-wrap">${filterXSS(assistantMessage)}</code>
                    </div>
                    </div>
                </li>`;
        for(var i = 0; i < totalItem; i++) {
            if (i % 2 != 0) {

            html += `<li class="chat-inbox-single-item chat-inbox-received">
                    <div class="chat-inbox-single-avatar">
                        <img src="${assistantImage}" alt="chat-robot">
                    </div>
                    <div>
                    <div class="chat-inbox-single-content">
                        <code class="font-Figtree whitespace-pre-wrap">${filterXSS(response[i].bot_message)}</code>
                    </div>
                    </div>
                </li>`;
            }
            else {
                html += `<li class="chat-inbox-single-item chat-inbox-sent ">
                <div class="chat-inbox-single-avatar">
                    <img src="${userImage}" alt="Rectangle-robot">
                </div>
                <div>
                    <div class="chat-inbox-single-content font-Figtree wrap-anywhere">
                        ${filterXSS(response[i].user_message)}
                    </div>
                </div>
            </li>`;
            }

        }

        $('#messageId').val(id)
        $('.chat-inbox-loader-overlay').hide();
        $('.chat-inbox-message-list').html(html);
        $(".chat-inbox-body").scrollTop($(".chat-inbox-body").prop("scrollHeight"));
    }

    $(document).on("click", ".chat-sidebar-user", function(e) {
        if ($(e.target).hasClass('chat-sidebar-user') || $(e.target).hasClass('editable-title')) {
            fetchData(this.id)
        }

    });

     /**
     * Submit the message form when the send button is clicked
     */
    $(document).on("click", ".chat-inbox-send-button", function (e) {
        var isExecuted = 0;
        let ask = filterXSS($('#message-to-send').val());
        if(ask.trim() === '') {
            return false;
        }

        
        const userImage = $('#user_image').attr('data-image');
        let div = $(".chat-inbox-body");
        let chatId = $('#messageId').val();
        let parentConversation = $('.chat-sidebar-users');
        let conversation = $('.list-' + chatId);
        let parentAssistant = $('.message-content');
        let assistant = $('.active-assistant');
        let botId = $('#botId').val();
        let botImage = $('.active-assistant').attr("data-image");
        let question = `<li class="chat-inbox-single-item chat-inbox-sent ">
                    <div class="chat-inbox-single-avatar">
                        <img src="${userImage}" alt="Rectangle-robot">
                    </div>
                    <div>
                        <div class="chat-inbox-single-content font-Figtree wrap-anywhere">
                            ${filterXSS($('#message-to-send').val())}
                        </div>
                    </div>
                </li>`;
        e.preventDefault();
        $('#message-to-send').val(''),
      

        $('.chat-inbox-message-list').append(question);
        $('.chat-bubble').show();
        div.scrollTop(div.prop("scrollHeight"));
        $(parentAssistant).prepend(assistant);
        $(parentConversation).prepend(conversation);
        var gethtml = '';

        fetch(SITE_URL + '/' + 'api/V1/user/openai/chat', {
            method: 'POST',
            body: JSON.stringify({
                promt: ask,
                chatId: chatId,
                botId: botId,
                dataType: 'json',
                _token: CSRF_TOKEN
            }),
            headers: {
                'Content-type': 'application/json',
                'stream-data': true,
                Authorization: `Bearer ${ACCESS_TOKEN}`
            },
        }) .then(async (res) => {
            let isAppend = true;
            if (!res.ok) {
                   // Log the error
            }
            const reader = res.body.getReader();
            const decoder = new TextDecoder();

            let text = "";
            while (true) {
                const { value, done } = await reader.read();
                if (done) break;
                text = decoder.decode(value, { stream: true });
                var numericValues = text.match(/\d+$/);
                text = text.replace(/\d+$/, '');
                function isJSONString(str) {
                    if (isExecuted == 0) {
                        try {
                            JSON.parse(str);
                            return true;
                        } catch (error) {
                            isExecuted = 1;
                            return false;
                        }
                    }
                    return false;
                }
                if (isJSONString(text)) {
                    var stringData = JSON.parse(text)
                    if (stringData.text) {
                        var message = stringData.text;
                        errorMessage(message, 'code-creation');
                        $('.chat-bubble').hide();
                        return;
                    }
                    var message = stringData.response.records.response ? stringData.response.records.response : stringData.text;
                    errorMessage(message, 'code-creation');
                        $('.chat-bubble').hide();
                        return;

                } else {
                    gethtml += filterXSS(text);
                    if (isAppend == true) {
                        isAppend = false;
                        let answer = `<li class="chat-inbox-single-item chat-inbox-received">
                                        <div class="chat-inbox-single-avatar">
                                            <img src="${botImage}" alt="chat-robot">
                                        </div>
                                        <div>
                                            <div class="chat-inbox-single-content">
                                                <code class="font-Figtree whitespace-pre-wrap chat-data">${gethtml}</code>
                                            </div>
                                        </div>
                                    </li>`;
                        $('.chat-bubble').hide();
                        $('.chat-inbox-message-list').append(answer);
                        
                    } else {
                        $('.chat-data:last').text(gethtml);
                    }
                }

                // Get the last word
                
                $('#messageId').val(numericValues);
            }

            div.scrollTop(div.prop("scrollHeight"));
        }).catch(error => {
                // Log the error
        });

    });
    

    $(document).on("click", ".chat-modal", function (e) {
        $('.delete-chat').attr('data-id', this.id); // sets
        e.preventDefault();
        $('.modal-parent').toggleClass('is-visible');
    });

    $(document).on('click', '.delete-chat', function () {
        var chatId = $(this).attr("data-id");
        doAjaxprocess(
            SITE_URL + "/user/delete-chat",
            {
                chatId : chatId,
                _token: CSRF_TOKEN
            },
            'post',
            'json'
        ).done(function(data) {
            $('.list-' + chatId).remove();
            toastMixin.fire({
                title: data.message,
                icon: data.status,
            });
            var ID = $('.chat-sidebar-user').first().attr('id');
            $('.modal-parent').toggleClass('is-visible');
            fetchData(ID)
        });
    });

    $(function() {
        $(document).on('click', '.edit-icon', function () {
            var editId = this.id
            var $titleContainer = $(this).closest('.title-container');
            var $title = $titleContainer.find('.editable-title');
            var currentValue = $title.text().trim();

            var $input = $('<input>', {
                type: 'text',
                value: currentValue
            });

            $title.replaceWith($input);
            $input.focus();

            $input.on('blur', function() {
                var newValue = $input.val();

                $input.replaceWith($('<p>', {
                class: 'editable-title',
                text: newValue
                }));

                doAjaxprocess(
                    SITE_URL + "/user/update-chat",
                    {
                        chatId : editId,
                        name : newValue,
                        _token: CSRF_TOKEN
                    },
                    'post',
                    'json'
                ).done(function(data) {

                });
            });
        });
    });

    var pageNumber = $('.chat-sidebar-users').data('next-page-url') ? $('.chat-sidebar-users').data('next-page-url').split("?page=")[1] : 0;
    var checked = true;

    $('.chat-view-sidebar').on('scroll', function(){
        checkIfAtEnd(this)
    });

    function checkIfAtEnd(contentContainer) {
        
        const contentHeight = contentContainer.scrollHeight;
        const visibleHeight = contentContainer.clientHeight;
        const scrollPosition = contentContainer.scrollTop;
        
        if ((scrollPosition + visibleHeight >= contentHeight) && pageNumber != 0 && pageNumber.length != 0 && checked) {
            checked = false;
            const parentDiv = $('.chat-sidebar-users');
            var assistantId = $('.active-assistant').attr('id');

            doAjaxprocess(
                SITE_URL + '/' + 'user/chat-conversation?page=' + pageNumber,
                {
                    id: assistantId,
                },
                'get',
                'json'
            ).done(function(response) {
                var sidebarHTML = response.html.data.map(function(item) {
                    return `
                        <div class="chat-sidebar-user border bg-[#3A3A39] border-[#474746] rounded chat-list list-${item.chat_conversation_id}" id="${item.chat_conversation_id}">
                            <div>
                                <div class="flex justify-between items-center relative title-container">
                                    <p class="editable-title text-white text-[13px]">${filterXSS(item.title)}</p>
                                    <div class="flex gap-2">
                                        <a class="text-white justify-center chat-modal hidden" href="javascript:void(0)" id="${item.chat_conversation_id}">
                                            <!-- Add your SVG code here -->
                                        </a>
                                        <a class="edit-icon text-white justify-center w-4 hidden" href="javascript:void(0)">
                                            <!-- Add your SVG code here -->
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
                parentDiv.append(sidebarHTML);
                parentDiv.removeAttr('data-next-page-url');
                pageNumber = response.html.next_page_url ? response.html.next_page_url.split("?page=")[1] : [];
                checked = true;
            });
        }
    }

/**
 * @param mixed url, which url hit this call
 * @param mixed params, paramters
 * @param mixed type, get, post
 * @param mixed dataType, json, html
 *
 * @return [type]
 */
function doAjaxprocess(url, params, type, dataType) {
    return $.ajax({
        data: params,
        url: url,
        type: type,
        dataType: dataType,
    });
}

function errorMessage(message, btnId) {
    toastMixin.fire({
        title: message,
        icon: 'error'
    });
    $(".loader").addClass('hidden');
    $('#'+ btnId).removeAttr('disabled');
}

$(document).ready(function() {
    dropDown();
    $(".plan-not-active").appendTo(".assistant-content");
});

function dropDown() {
    $('.collapse-button').on('click',function() {
        $('.chat-sidebar').toggleClass('sidebar_small');
        $('.chat-content').toggleClass('main-content_large');
        $('.full-content-icon').toggleClass('hidden');
        $('.half-content-icon').toggleClass('hidden');
        $('.new-chat').toggleClass('opacity-0');
        
    });

    $('.chat-dropdown').on("click", function(event){
        event.stopPropagation();
        $(".chat-dropdown-content").slideToggle(200);
    });

    $('.search-input').on("click", function(event){
        event.stopPropagation();  // Prevents the click from reaching the document handler
    });
    $('.chat-dropdown-content').on("click", function(event){
        event.stopPropagation();  // Prevents the click from reaching the document handler
    });
    $(document).on("click", function () {
        $(".chat-dropdown-content").hide();
    });
}

$(".chat-search-input").on("keyup", function() {
    var value = this.value.toLowerCase().trim();
    $(".user-name").each(function() {
      var $parentDiv = $(this).closest('.search-content');
      $parentDiv.toggle($(this).text().toLowerCase().trim().includes(value));
    });
  });

function fetchChatBot(e) {
    const ChatBotId = e.id;
    $.ajax({
        url: SITE_URL + '/' + 'user/chat/bot',
        type: "get",
        beforeSend: function (xhr) {
            $(".chat-view-container").removeClass('chat-hidden');
            $(".message-content").addClass("opacity-1");

            const html = `
                <div class="loader-template items-center h-[20vh]">
                    <svg class="animate-spin h-7 w-7 m-auto mt-[100px]" xmlns="http://www.w3.org/2000/svg" width="72" height="72" viewBox="0 0 72 72" fill="none">
                        <mask id="path-1-inside-1_1032_3036" fill="white">
                        <path d="M67 36C69.7614 36 72.0357 38.2493 71.6534 40.9841C70.685 47.9121 67.7119 54.4473 63.048 59.7573C57.2779 66.3265 49.3144 70.5713 40.644 71.6992C31.9736 72.8271 23.1891 70.761 15.9304 65.8866C8.67173 61.0123 3.4351 53.6628 1.19814 45.2104C-1.03881 36.7579 -0.123172 27.7803 3.77411 19.9534C7.67139 12.1266 14.2839 5.98568 22.3772 2.67706C30.4704 -0.631565 39.4912 -0.881694 47.7554 1.97337C54.4353 4.28114 60.2519 8.49021 64.5205 14.0322C66.2056 16.2199 65.3417 19.2997 62.9417 20.6656L60.8567 21.8524C58.4567 23.2183 55.4379 22.3325 53.5977 20.2735C50.9338 17.2927 47.5367 15.0161 43.7066 13.6929C38.2888 11.8211 32.3749 11.9851 27.0692 14.1542C21.7634 16.3232 17.4284 20.3491 14.8734 25.4802C12.3184 30.6113 11.7181 36.4969 13.1846 42.0381C14.6511 47.5794 18.0842 52.3975 22.8428 55.5931C27.6014 58.7886 33.3604 60.1431 39.0445 59.4037C44.7286 58.6642 49.9494 55.8814 53.7321 51.5748C56.4062 48.5302 58.2325 44.8712 59.0732 40.9628C59.6539 38.2632 61.8394 36 64.6008 36H67Z" />
                        </mask>
                        <path d="M67 36C69.7614 36 72.0357 38.2493 71.6534 40.9841C70.685 47.9121 67.7119 54.4473 63.048 59.7573C57.2779 66.3265 49.3144 70.5713 40.644 71.6992C31.9736 72.8271 23.1891 70.761 15.9304 65.8866C8.67173 61.0123 3.4351 53.6628 1.19814 45.2104C-1.03881 36.7579 -0.123172 27.7803 3.77411 19.9534C7.67139 12.1266 14.2839 5.98568 22.3772 2.67706C30.4704 -0.631565 39.4912 -0.881694 47.7554 1.97337C54.4353 4.28114 60.2519 8.49021 64.5205 14.0322C66.2056 16.2199 65.3417 19.2997 62.9417 20.6656L60.8567 21.8524C58.4567 23.2183 55.4379 22.3325 53.5977 20.2735C50.9338 17.2927 47.5367 15.0161 43.7066 13.6929C38.2888 11.8211 32.3749 11.9851 27.0692 14.1542C21.7634 16.3232 17.4284 20.3491 14.8734 25.4802C12.3184 30.6113 11.7181 36.4969 13.1846 42.0381C14.6511 47.5794 18.0842 52.3975 22.8428 55.5931C27.6014 58.7886 33.3604 60.1431 39.0445 59.4037C44.7286 58.6642 49.9494 55.8814 53.7321 51.5748C56.4062 48.5302 58.2325 44.8712 59.0732 40.9628C59.6539 38.2632 61.8394 36 64.6008 36H67Z" stroke="url(#paint0_linear_1032_3036)" stroke-width="24" mask="url(#path-1-inside-1_1032_3036)" />
                        <defs>
                        <linearGradient id="paint0_linear_1032_3036" x1="46.8123" y1="63.1382" x2="21.8195" y2="6.73779" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#E60C84" />
                            <stop offset="1" stop-color="#FFCF4B" />
                        </linearGradient>
                        </defs>
                    </svg>
                </div> `;
            $('.message-content').html(html);
        },
        data: {
            id: ChatBotId,
        },
        success: function(response) {
            
            $(".chat-view-container").html(response.html);
            $(".chat-toggle-button").addClass('chat-hidden');
            if (response.chat.length != 0 ) {
                $('.list-'+ response.id).addClass('chat-user-active');
                appendData(response.chat, response.id);
                
            }
            dropDown();

            $('.search-input').on("click", function(event){
                event.stopPropagation();  // Prevents the click from reaching the document handler
            });

            $(".chat-search-input").on("keyup", function() {
                var value = this.value.toLowerCase().trim();
                $(".user-name").each(function() {
                  var $parentDiv = $(this).closest('.search-content');
                  $parentDiv.toggle($(this).text().toLowerCase().trim().includes(value));
                });
            });
        },
        complete: function() {
            $(".plan-not-active").appendTo(".assistant-content");
        },
        error: function(response) {
            var jsonData = JSON.parse(response.responseText);
            errorMessage(jsonData.response.status.message, 'code-creation');
         }
    });
}
  
  
  