"use strict";

var firstValueOfDropedown;
$(document).ready(function () {
    var currentUrl = window.location.href.indexOf("content/edit") > -1;
    currentUrl ? $(".content-update").show() : $(".content-update").hide();
});

$(document).on("keyup", ".questions", function () {
    $(this).siblings(".character-count")
        .text($(this).val().length + "/" + $(this).attr("maxlength"));
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

$(document).on("click", ".content-update", function () {

    if(!tinymce.activeEditor.getContent({format : 'text'})) {
        toastMixin.fire({
            title: jsLang("Nothing To Update"),
            icon: "error",
        });
        return true;
    }
    
    var parts = window.location.href.split("/");
    $(".loader-update").removeClass("hidden");
    $('.content-update').addClass('cursor-not-allowed');
    
    doAjaxprocess(
        SITE_URL + "/user/update-content",
        {
                contentSlug: parts[parts.length-1],
                content: tinymce.activeEditor.getContent(),
                _token: CSRF_TOKEN
        },
        'post',
        'json'
    ).done(function(data) {
        $('.content-update').removeClass('cursor-not-allowed');
        $('.loader-update').addClass('hidden');
        toastMixin.fire({
            title: data.message,
            icon: data.status,
        });
    });
});

$(document).on("click", ".saved-content", function () {
    if ($(".partialContent-" + this.id).length) {
        return true;
    }
    $("#partial-history").html("");
    $(".loader-history").removeClass("hidden");
    $(".save-content-" + this.id).removeClass("border-design-3");
    $(".saved-content").removeClass("border-design-3-active");
    $(".save-content-" + this.id).addClass("border-design-3-active");

    doAjaxprocess(
        SITE_URL + "/user/get-content",
        {
            contentId: this.id
        },
        'get',
        'html'
    ).done(function(data) {

        $('.loader-history').addClass('hidden');
        $("#partial-history").append(data);
    });
});
$(document).on("click", ".modal-toggle", function (e) {
    $('.delete-image').attr('data-id', $(this).attr('id')); // sets
    $('.delete-code').attr('data-id', $(this).attr('id')); // sets
    $('.delete-content').attr('data-id', $(this).attr('id')); // sets
    e.preventDefault();
    $('.index-modal').toggleClass('is-visible');
});

$(document).on('click', '.delete-content', function () {
    var contentId = $(this).attr("data-id");
    doAjaxprocess(
        SITE_URL + "/user/deleteContent",
        {
            contentId : contentId,
        },
        'get',
        'json'
    ).done(function(data) {
        $('#partial-history').html('');
        $('.save-content-' + contentId).hide();
        toastMixin.fire({
            title: data.message,
            icon: "success",
        });
        $('#document_'+contentId).remove();
    });
});

$(document).on('click', '.delete-code', function () {
    var id = $(this).attr("data-id");
    doAjaxprocess(
        SITE_URL + "/user/code/delete",
        {
            id : $(this).attr("data-id"),
            _token: CSRF_TOKEN
        },
        'post',
        'json'
    ).done(function(data) {
        toastMixin.fire({
            title: data.message,
            icon: data.status,
        });
        $('#code_'+id).remove();
    });
});

$(document).on('click', '.delete-image', function () {
    var id = $(this).attr("data-id");
    doAjaxprocess(
        SITE_URL + "/user/delete-image",
        {
            id : $(this).attr("data-id"),
            _token: CSRF_TOKEN
        },
        'post',
        'json'
    ).done(function(data) {
        toastMixin.fire({
            title: data.message,
            icon: data.status,
        });
        $('#image_'+id).remove();
    });
});

$(document).on("click", "#history", function () {
    $.ajax({
        url: SITE_URL + "/api/openai/history",
        type: "get",
        dataType: "html",
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", "Bearer " + ACCESS_TOKEN);
        },

        success: function (data) {
            var demo = JSON.parse(data);
            $(".overflow-hidden").html(demo.response.records.html);
        },
        error: function(data) {
         }
    });
});

$(document).on("click", ".copy-code", function () {
    var codeElement = $(this).siblings('code');
    var code = codeElement.text();
    if (code) {
        var message = jsLang("Code Copied Successfully");
        var icon = "success";
    } else {
        var message = jsLang("Nothing To Copy");
        var icon = "error";
    }

    navigator.clipboard.writeText(code);

    toastMixin.fire({
        title: message,
        icon: icon,
    });
});

function download(filename, text) {
    var element = document.createElement('a');
    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
    element.setAttribute('download', filename);

    element.style.display = 'none';
    document.body.appendChild(element);

    element.click();

    document.body.removeChild(element);
}

// Start file download.
$(document).on('click', '#download-code', function() {
    var code = $('.code').text();
    if(code) {
       return download("code.txt", $('.code').text());
    } else {
        var message = jsLang("Nothing To Download");
        var icon = "error";

    }
    navigator.clipboard.writeText($('.code').text());

    toastMixin.fire({
        title: message,
        icon: icon,
    });

});

$(document).on("click", ".image-save", function () {
    var imageSrc = [];
    $("#image-content")
        .children("img")
        .map(function () {
            imageSrc.push($(this).attr("src"));
        });
    if (imageSrc.length === 0) {
        toastMixin.fire({
            title: jsLang("No Imge Found"),
            icon: "error",
        });
    }
    doAjaxprocess(
        SITE_URL + "/user/save-image",
        {
            imageSource : imageSrc,
            promt : $('#image-description').val(),
            size : $('#size').val(),
            artStyle : $('#art-style').val(),
            artStyle : $('#art-style').val(),
            lightingStyle : $('#ligting-style').val(),
            _token: CSRF_TOKEN
        },
        'post',
        'json'
    ).done(function(data) {
        toastMixin.fire({
            title: data.message,
            icon: "success",
        });
    });
});

$(document).on('click', '.generate-pdf', function () {

    var myContent = tinymce.activeEditor.getContent({format : 'raw'});
    if(!tinymce.activeEditor.getContent({format : 'text'})) {
        flashMessage();
        return true;
    }
    const options = {
        margin: 0.3,
        filename: 'document.pdf',
        image: {
            type: 'jpeg',
            quality: 0.98
        },
        html2canvas: {
            scale: 2
        },
        jsPDF: {
            unit: 'in',
            format: 'a4',
            orientation: 'portrait'
        }
    }

    html2pdf().from(myContent).set(options).save();
});

$(document).on("click", ".generate-word", function () {
    var header =
        "<html xmlns:o='urn:schemas-microsoft-com:office:office' " +
        "xmlns:w='urn:schemas-microsoft-com:office:word' " +
        "xmlns='http://www.w3.org/TR/REC-html40'>" +
        "<head><meta charset='utf-8'><title>Export HTML to Word Document with JavaScript</title></head><body>";
    var myContent = tinymce.activeEditor.getContent({ format: "raw" });
    if(!tinymce.activeEditor.getContent({ format: 'text' })) {
        flashMessage();
        return true;
    }
    $("#basic-example").val(myContent);
    var contentOfHtml = $("#basic-example").val();
    var footer = "</body></html>";
    var sourceHTML = header + contentOfHtml + footer;

   var source = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(sourceHTML);
   var fileDownload = document.createElement("a");
   document.body.appendChild(fileDownload);
   fileDownload.href = source;
   fileDownload.download = 'document.doc';
   fileDownload.click();
   document.body.removeChild(fileDownload);
});

function flashMessage() {

    toastMixin.fire({
        title: jsLang("Nothing To Download"),
        icon: "error",
    });

}

$(document).on("click", ".copy-text", function () {
    var myContent = tinymce.activeEditor.getContent({format : 'text'});
    if(myContent) {
        var message = jsLang("Content Copied Successfully");
        var icon = "success";
    } else {
        var message = jsLang("Nothing To Copy");
        var icon = "error";

    }
    navigator.clipboard.writeText(myContent);

    toastMixin.fire({
        title: message,
        icon: icon,
    });
});
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

$(document).on("change", ".use-cases", function () {
    if (!(window.location.href.indexOf("content/edit") > -1)) {
        $(".content-name").html("Content Of a" + " " + $(this).val());
    }

    $(".edit-url").attr("href", $(this).val());
    $.ajax({
        url: SITE_URL + "/user/formfiled-usecase/" + $(this).val(),
        type: "get",
        dataType: "html",
        data: {
            useCae: $(this).val(),
            _token: CSRF_TOKEN,
        },
        beforeSend: () => {
            $(".documents-input-loader").removeClass("hidden");
            $("#appended-data").addClass("hidden");
        },
        success: function (response) {
            $("#appended-data").html(response);
        },
        complete: () => {
            $(".documents-input-loader").addClass("hidden");
            $("#appended-data").removeClass("hidden");
        }
    });
});

function nl2br(str, is_xhtml) {
    var breakTag =
        is_xhtml || typeof is_xhtml === "undefined" ? "<br />" : "<br>";
    return (str + "").replace(
        /([^>\r\n]?)(\r\n|\n\r|\r|\n)/g,
        "$1" + breakTag + "$2"
    );
}

$(document).on("submit", "#openai-form", function (e) {
    let contentSlug = "";
    var currentUrl = window.location.href.indexOf("content/edit") > -1;
    if (currentUrl) {
        (parts = window.location.href.split("/")),
            (contentSlug = parts[parts.length - 1]);
    }

    e.preventDefault();
    var gethtml = tinyMCE.activeEditor.getContent();

    var formData = {};
    $(".dynamic-input").each(function (column) {
        formData[this.name] = $(this).val();
    });
    $.ajax({
        url: SITE_URL + "/" + PROMT_URL,
        type: "POST",
        beforeSend: function (xhr) {
            $(".loader").removeClass('hidden');
            $("#magic-submit-button").attr("disabled", "disabled");
            xhr.setRequestHeader("Authorization", "Bearer " + ACCESS_TOKEN);
        },
        data: {
            questions: JSON.stringify(formData),
            promt: $("#promt").val(),
            useCase: $(".use-cases").val(),
            language: $("#language").val(),
            variant: $("#variant").val(),
            temperature: $("#temperature").val(),
            tone: $("#tone").val(),
            previousContent: gethtml,
            contentSlug: contentSlug,
            _token: CSRF_TOKEN,
        },
        success: function (data) {
            if (data.response.message) {
                errorMessage(data.response.message);
                return true;
            }
            var totalItems = data.response.records.choices.length;
            for (let i = 0; i < totalItems; i++) {
                if (data.response.records.object == 'text_completion') {
                    gethtml += nl2br(filterXSS(data.response.records.choices[i].text));
                } else {
                    gethtml += nl2br(filterXSS(data.response.records.choices[i].message.content));
                }

                gethtml += "<br>";
                
                if ( totalItems > 1 && totalItems-1 > i ) {
                    gethtml += "<hr>";
                }
            }
            tinyMCE.activeEditor.setContent(gethtml, { format: "raw" });
            $(".word-counter").html(
                "Total Word: " + data.response.records.words
            );
            $(".loader").addClass('hidden');
            $("#magic-submit-button").removeAttr("disabled");

            var total_word_left = $('.total-word-left');
            var total_word_used = $('.total-word-used');
            var credit_limit = $('.credit-limit');
            if (credit_limit.length > 0) {
                var word_left_count = jsLang('Unlimited');
                if (total_word_left.text() != jsLang('Unlimited')) {
                    word_left_count = Number(total_word_left.text()) - data.response.records.words;
                }

                var word_used_count = Number(total_word_used.text()) + data.response.records.words;

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
        error: function (data) {
            var jsonData = JSON.parse(data.responseText);
            var message = jsonData.response.records.response ? jsonData.response.records.response : jsonData.response.status.message
            errorMessage(message, 'magic-submit-button');
         }
    });
});

$('select').each(function() {
    var tomSelect = new TomSelect(this, {
        onFocus: function() {
            firstValueOfDropedown = tomSelect.getValue(0);
          },
        onChange: function(value) {
            if (value.length > 0) {
            firstValueOfDropedown = value;
            }

            if (value === '') {
                tomSelect.setValue(firstValueOfDropedown);
            }
        }
    });
  });


function errorMessage(message, btnId)
{
    toastMixin.fire({
        title: message,
        icon: 'error'
      });
      $(".loader").addClass('hidden');
      $('#'+ btnId).removeAttr('disabled');
}


$(document).on('submit', '#openai-image-form', function (e) {
    var gethtml = '';
    e.preventDefault();
    $.ajax({
        url: SITE_URL + "/" + PROMT_URL,
        type: "POST",
        beforeSend: function (xhr) {
            $(".loader").removeClass('hidden');
            $("#image-creation").attr("disabled", "disabled");
            xhr.setRequestHeader("Authorization", "Bearer " + ACCESS_TOKEN);
        },
        data: {
            promt: filterXSS($("#image-description").val()),
            variant: $("#variant").val(),
            resulation: $("#size").val(),
            artStyle: $("#art-style").val(),
            lightingStyle: $("#ligting-style").val(),
            file: $("#file_input").val(),
            dataType: "json",
            _token: CSRF_TOKEN,
        },
        success: function(response) {
            $(".static-image-text").addClass('hidden');
            var credit = $('.image-credit-remaining');
            if (!isNaN(credit.text()) && response.response.records != null) {
                credit.text(credit.text() - response.response.records.length);
            }

            gethtml +='<div class="flex flex-wrap justify-center items-center md:gap-6 gap-5 mt-10 image-content1 9xl:mx-32 3xl:mx-16 2xl:mx-5">'
                $.each(response.response.records, function(key,valueObj) {
                    gethtml +='<div class="relative md:w-[300px] md:h-[300px] w-[181px] h-[181px] download-image-container md:rounded-xl rounded-lg">'
                    gethtml += '<img class="m-auto md:w-[300px] md:h-[300px] w-[181px] h-[181px] cursor-pointer md:rounded-xl rounded-lg border border-color-DF dark:border-color-3A"src="'+ valueObj['url'] +'" alt=""><div class="image-hover-overlay"></div>'
                    gethtml +='<div class=" flex gap-3 right-3 bottom-3 absolute">'
                    gethtml += '<div class="image-download-button"><a class="relative tooltips w-9 h-9 flex items-center m-auto justify-center" href="'+ valueObj['slug_url'] +'">'
                    gethtml +=`<img class="w-[18px] h-[18px]" src="${SITE_URL}/Modules/OpenAI/Resources/assets/image/view-eye.svg" alt="">`
                    gethtml +='<span class="image-download-tooltip-text z-50 w-max text-white items-center font-medium text-12 rounded-lg px-2.5 py-[7px] absolute z-1 top-[138%] left-[50%] ml-[-22px]">View</span>'
                    gethtml += '</a>'
                    gethtml += '</div>'
                    gethtml += '<div class="image-download-button"><a class="relative tooltips w-9 h-9 flex items-center m-auto justify-center" href="'+ valueObj['url'] +'" download="'+ filterXSS(valueObj['name']) +'" Downlaod>'
                    gethtml +=`<img class="w-[18px] h-[18px]" src="${SITE_URL}/Modules/OpenAI/Resources/assets/image/file-download.svg" alt="">`
                    gethtml +='<span class="image-download-tooltip-text z-50 w-max text-white items-center font-medium text-12 rounded-lg px-2.5 py-[7px] absolute z-1 top-[138%] left-[50%] ml-[-38px]">Download</span>'
                    gethtml += '</a>'
                    gethtml += '</div>'
                    gethtml += '</div>'
                    gethtml += '</div>'

                });
                gethtml += '</div>';

                $('#image-content').prepend(gethtml);
                $(".loader").addClass('hidden');
                $('#image-creation').removeAttr('disabled');
        },
        error: function (response) {
            var jsonData = JSON.parse(response.responseText);

            if(jsonData.response.records === null) {
                errorMessage(jsonData.response.status.message, 'image-creation');
                return true;
            }

            var message = jsonData.response.records.response ? jsonData.response.records.response : jsonData.response.status.message;
            errorMessage(message, 'image-creation');
         }
    });
});

$(document).on('submit', '#openai-code-form', function (e) {
    e.preventDefault();

    $.ajax({
        url: SITE_URL + '/' + PROMT_URL,
        type: "POST",
        beforeSend: function (xhr) {
          $(".loader").removeClass('hidden');
            $('#code-creation').attr('disabled', 'disabled');
            xhr.setRequestHeader('Authorization', 'Bearer ' + ACCESS_TOKEN);
        },
        data: {
            promt: filterXSS($("#code-description").val()),
            language: $("#language").val(),
            codeLabel: $("#code-level").val(),
            dataType: 'json',
            _token: CSRF_TOKEN
        },
        success: function(response) {
            $(".static-code-text").addClass('hidden');
            if (response.response.message) {
                errorMessage(response.response.message);
                return true;
            }

            var strArray = response.response.records.choices[0].message.content.split("```");
            var totalItem = strArray.length;
            var html = '';
            for(var i = 0; i < totalItem; i++) {
                if (i % 2 != 0) {
                    html += '<div><pre class="area relative" data-language="php" id="codetext"><code class="!pt-10 code">' + filterXSS(strArray[i]) + '</code><a href="javaScript:void(0);" class="absolute flex gap-2 items-center justify-center text-color-14 bg-white md:py-2.5 py-1.5 md:px-5 px-3 border border-color-89 rounded-lg top-4 right-4 font-semibold font-Figtree copy-code"><svg xmlns="http://www.w3.org/2000/svg" width="19" height="18" viewBox="0 0 19 18"fill="none"><g clip-path="url(#clip0_3914_2023)"><path d="M12.5 0.75H3.5C2.675 0.75 2 1.425 2 2.25V12.75H3.5V2.25H12.5V0.75ZM11.75 3.75L16.25 8.25V15.75C16.25 16.575 15.575 17.25 14.75 17.25H6.4925C5.6675 17.25 5 16.575 5 15.75L5.0075 5.25C5.0075 4.425 5.675 3.75 6.5 3.75H11.75ZM11 9H15.125L11 4.875V9Z" fill="#141414" /></g><defs><clipPath id="clip0_3914_2023"><rect width="18" height="18" fill="white" transform="translate(0.5)" /></clipPath></defs></svg> <span>'+copy+'</span> </a> </pre></div>';
                }
                else {
                     html += '<div class="context-area my-5 text-15 text-color-14 dark:text-white width-code break-words ">' + filterXSS(strArray[i]) + '</div>';
                }
            }

            $('.code-area').html(html)
            hljs.highlightAll();

            var total_word_left = $('.total-word-left');
            var total_word_used = $('.total-word-used');
            var credit_limit = $('.credit-limit');

            if (credit_limit.length > 0) {
                var word_left_count = jsLang('Unlimited');
                if (total_word_left.text() != jsLang('Unlimited')) {
                    word_left_count = Number(total_word_left.text()) - response.response.records.usage.words;
                }

                var word_used_count = Number(total_word_used.text()) + response.response.records.usage.words;

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
        complete: () => {
            $(".loader").addClass('hidden');
            $('#code-creation').removeAttr('disabled');
        },
        error: function(response) {
            var jsonData = JSON.parse(response.responseText);

            if(jsonData.response.records === null) {
                errorMessage(jsonData.response.status.message, 'code-creation');
                return true;
            }

            var message = jsonData.response.records.response ? jsonData.response.records.response : jsonData.response.status.message
            errorMessage(message, 'code-creation');
         }
    });
});

if ($(".code-view-area").find("#code-view-content").length) {
    $(document).ready(function () {
        hljs.highlightAll();
    });
}

$(document).ready(function(){
    $('.dropdown-click').on("click",function(event){
        event.stopPropagation();
         $(".drop-down").slideToggle(200);
    });
  });
  $(document).on("click", function () {
    $(".drop-down").hide();
  });

  setTimeout(() => {
        $('iframe#basic-example_ifr').contents().on('click', function(event) {  $(".drop-down").hide(); });
        $('.tox.tox-tinymce').contents().on('click', function(event) {  $(".drop-down").hide(); });
  }, 1000);
$(document).ready(function(){
    $('.dot-click').on("click",function(event){
        event.stopPropagation();
        $(this).closest(".drop-parents").find(".drop-body").slideToggle(200);
    });
  });
  $(document).on("click", function () {
    $(".drop-body").hide();
  });

