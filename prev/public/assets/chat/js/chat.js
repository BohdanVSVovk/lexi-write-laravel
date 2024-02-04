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



    $(document).on("keyup", "#message-to-send", function (e) {
        if (e.keyCode === 13 && !e.shiftKey) {
            $('.chat-inbox-send-button').trigger('click');
        }
    });

    $(document).on('click', '.new-chat', function(){
        $('.chat-inbox-message-list').html('');
        $('#messageId').val('')
        $("#message-to-send").trigger("focus");
    });

    function fetchData(id)
    {
        let html = '';
        let div = $(".chat-inbox-body");
        $.ajax({
            url: SITE_URL + '/' + 'user/chat-history/' + id,
            type: "get",
            beforeSend: function (xhr) {
                $('.chat-inbox-message-list').html('');
                $('.chat-inbox-loader-overlay').show();
                $(".chat-sidebar-user").removeClass("chat-user-active");
                $('#'+ id).addClass('chat-user-active')
            },
            data: {
                id: id,
            },
            success: function(response) {
                let userImage = $('#user-img').attr('data-url');
                if (response.error) {
                    errorMessage(response.error.message);
                    return true;
                }
                var totalItem = response.length;
                for(var i = 0; i < totalItem; i++) {
                    if (i % 2 != 0) {

                    html += `<li class="chat-inbox-single-item chat-inbox-received">
                         <div class="chat-inbox-single-avatar">
                             <img src="${botImage}" alt="chat-robot">
                         </div>
                         <div>
                             <div class="chat-inbox-single-content">
                             ${filterXSS(response[i].bot_message)}
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
                            <div class="chat-inbox-single-content">
                                ${filterXSS(response[i].user_message)}
                            </div>
                        </div>
                    </li>`;
                    }

                }

            $('#messageId').val(id)
            $('.chat-inbox-loader-overlay').hide();
            $('.chat-inbox-message-list').html(html);
            div.scrollTop(div.prop("scrollHeight"));
            },

            error: function(response) {
                var jsonData = JSON.parse(response.responseText);
                errorMessage(jsonData.response.status.message, 'code-creation');
             }
        });
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
        let ask = filterXSS($('#message-to-send').val());
        if(ask.trim() === '') {
            return false;
        }
        const userImage = $('#user_image').attr('data-image');
        let div = $(".chat-inbox-body");

        let chatId = $('#messageId').val()
        let question = `<li class="chat-inbox-single-item chat-inbox-sent ">
                    <div class="chat-inbox-single-avatar">
                        <img src="${userImage}" alt="Rectangle-robot">
                    </div>
                    <div>
                        <div class="chat-inbox-single-content">
                            ${filterXSS($('#message-to-send').val())}
                        </div>
                    </div>
                </li>`;
        e.preventDefault();
        $('#message-to-send').val(''),

        $.ajax({
            url: SITE_URL + '/' + 'api/V1/user/openai/chat',
            type: "POST",
            beforeSend: function (xhr) {
            $('.chat-inbox-message-list').append(question);
            $('.chat-bubble').show();
            div.scrollTop(div.prop("scrollHeight"));
                xhr.setRequestHeader('Authorization', 'Bearer ' + ACCESS_TOKEN);
            },

            data: {
                promt: ask,
                chatId: chatId,
                dataType: 'json',
                _token: CSRF_TOKEN
            },
            success: function(response) {
                if (response.error) {
                    errorMessage(response.error.message);
                    $('.chat-bubble').hide();
                    return true;
                }

                response = response.response.records;

                let answer = `<li class="chat-inbox-single-item chat-inbox-received">
                            <div class="chat-inbox-single-avatar">
                                <img src="${botImage}" alt="chat-robot">
                            </div>
                            <div>
                                <div class="chat-inbox-single-content">
                                ${filterXSS(response['apiResponse'].choices[0].message.content)}
                                </div>
                            </div>
                        </li>`;
                        $('.chat-bubble').hide();
                    $('.chat-inbox-message-list').append(answer);


                // Add new chat header
                var header = `<div class="chat-sidebar-user border border-[#898989] rounded chat-list chat-user-active list-${response['id']}" id=${response['id']}>
                            <div>
                                <div class="flex justify-between relative title-container">
                                    <p class="editable-title text-white text-left text-[13px]">${filterXSS(ask)}</p>
                                    <div class="flex gap-2">
                                        <a class="text-white justify-center chat-modal opacity-0" href="javascript: void(0)" id="${response['id']}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M3.84615 2.8C3.37884 2.8 3 3.15817 3 3.6V4.4C3 4.84183 3.37884 5.2 3.84615 5.2H4.26923V12.4C4.26923 13.2837 5.0269 14 5.96154 14H11.0385C11.9731 14 12.7308 13.2837 12.7308 12.4V5.2H13.1538C13.6212 5.2 14 4.84183 14 4.4V3.6C14 3.15817 13.6212 2.8 13.1538 2.8H10.1923C10.1923 2.35817 9.81347 2 9.34615 2H7.65385C7.18653 2 6.80769 2.35817 6.80769 2.8H3.84615ZM6.38462 6C6.61827 6 6.80769 6.17909 6.80769 6.4V12C6.80769 12.2209 6.61827 12.4 6.38462 12.4C6.15096 12.4 5.96154 12.2209 5.96154 12L5.96154 6.4C5.96154 6.17909 6.15096 6 6.38462 6ZM8.5 6C8.73366 6 8.92308 6.17909 8.92308 6.4V12C8.92308 12.2209 8.73366 12.4 8.5 12.4C8.26634 12.4 8.07692 12.2209 8.07692 12V6.4C8.07692 6.17909 8.26634 6 8.5 6ZM11.0385 6.4V12C11.0385 12.2209 10.849 12.4 10.6154 12.4C10.3817 12.4 10.1923 12.2209 10.1923 12V6.4C10.1923 6.17909 10.3817 6 10.6154 6C10.849 6 11.0385 6.17909 11.0385 6.4Z" fill="currentColor"></path>
                                            </svg>
                                        </a>
                                        <a class="edit-icon text-white justify-center opacity-0 w-4" href="javascript: void(0)">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M2.73266 10.0443L2.01789 13.1291C1.99323 13.2419 1.99407 13.3587 2.02036 13.4711C2.04665 13.5835 2.09771 13.6886 2.16982 13.7787C2.24193 13.8689 2.33326 13.9418 2.43715 13.9921C2.54104 14.0424 2.65485 14.0689 2.77028 14.0696C2.82407 14.075 2.87826 14.075 2.93205 14.0696L6.03568 13.3548L11.9947 7.41841L8.66906 4.10034L2.73266 10.0443Z" fill="currentColor"/>
                                                <path d="M13.8682 4.44626L11.6486 2.22669C11.5027 2.0815 11.3052 2 11.0993 2C10.8935 2 10.696 2.0815 10.5501 2.22669L9.31616 3.46062L12.638 6.78245L13.8719 5.54852C13.9441 5.47594 14.0013 5.38984 14.0402 5.29514C14.0791 5.20043 14.099 5.09899 14.0986 4.99661C14.0983 4.89423 14.0777 4.79292 14.0382 4.69849C13.9986 4.60405 13.9409 4.51834 13.8682 4.44626Z" fill="currentColor"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>`;
                if (response['newChatId'] == null) {
                    $(".chat-sidebar-user").removeClass("chat-user-active");
                    $('#'+ response['id']).addClass('chat-user-active')
                    $('.chat-sidebar-users').prepend(header);
                    $('#messageId').val(response['id']);
                }
                div.scrollTop(div.prop("scrollHeight"));

                var total_word_left = $('.total-word-left');
                var total_word_used = $('.total-word-used');
                var credit_limit = $('.credit-limit');

                if (credit_limit.length > 0) {
                    var word_left_count = jsLang('Unlimited');
                    if (total_word_left.text() != jsLang('Unlimited')) {
                        word_left_count = Number(total_word_left.text()) - response.usage.words;
                    }
                    
                    var word_used_count = Number(total_word_used.text()) + response.usage.words;

                    if (word_left_count < 0) {
                        word_left_count = 0;
                    }

                    if (Array.isArray(Number(credit_limit.text().match(/(\d+)/))) && word_used_count > Number(credit_limit.text().match(/(\d+)/)[0])) {
                        word_used_count = Number(credit_limit.text().match(/(\d+)/)[0]);
                    }

                    total_word_left.text(word_left_count);
                    total_word_used.text(word_used_count);
                }

            },

            error: function(response) {
                $('.chat-bubble').hide();
                var jsonData = JSON.parse(response.responseText);
                var message = jsonData.response.records;

                if (typeof message == 'object') {
                    message = jsonData.response.records.response
                }

                errorMessage(message, 'code-creation');
            }
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


function errorMessage(message, btnId)
    {
        toastMixin.fire({
            title: message,
            icon: 'error'
        });
        $(".loader").addClass('hidden');
        $('#'+ btnId).removeAttr('disabled');
    }
