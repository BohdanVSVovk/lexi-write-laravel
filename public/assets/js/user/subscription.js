"use strict";

var response = {
    status: $('.alert-danger').length > 0 ? 'failed' : 'success',
    message: $('.reset-success-msg').text()
}

$(".billing-information").on('click', function () {
    $(".billing-information-modal").css("display", "flex");
    $(".billing-modal-container").show();
});
$(".modal-close-btn").on('click', function () {
    $(".billing-information-modal, .upgradePlan-information-modal").css("display", "none");
});
$(".upgrade-plan").on('click', function () {
    $(".upgradePlan-information-modal").css("display", "flex");
    $(".upgradePlan-modal-container").show();
});

$(document).on("click", '.plan', function () {
    const packageId = $(this).val();
    getPlan(packageId);

});

function getPlan(packageId) {
    $.ajax({
        url: SITE_URL + `/plan-description/${packageId}`,
        type: "GET",
        beforeSend: function() {
            setTimeout(() => {
                $(".checked-loader").block({
                    message: `<div class="flex justify-center">
                <svg class="animate-spin text-gray-700 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="#000" stroke-width="3"></circle>
                <path class="opacity-75" fill="#fff" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
                </div>`,
                    css: {
                        backgroundColor: "transparent",
                        border: "none",
                    },
                });
            }, 5);
        },
        success: function(result) {
            $('#plans').find('.plan-description').replaceWith(result);
            $(".checked-loader").unblock();
        }
    });
}
$(document).on("submit", ".plan-form", function () {
    $('.update-plan-loader').removeClass('hidden');
    setTimeout(() => {
    $('.update-plan-loader').addClass('hidden');
}, 6000);
});

if ($(".subscription-details").length > 0) {
    setTimeout(() => {
        (window["ReactNativeWebView"]||window).postMessage(JSON.stringify(response));
    }, 100);
}

//Plan Pricing
$('input[name="check_billing"]').on('change', function() {
    var value = $(this).val();
    $('.plan-parent').addClass('hidden');
    
    if ($(`.plan-${value}`).length == 0) {
        $('.plan-root').append(`
            <div class="plan-parent plan-${value}">
                <p class="text-color-14 dark:text-white mx-auto text-[22px] leading-6 font-semibold px-5 break-words text-center">${jsLang('No plan available under this category')}</p>
            </div>
        `)
    } else {
        $(`.plan-${value}`).removeClass('hidden');
    }
})


if (typeof is_onetime !== 'undefined' && is_onetime) {
    $('#tabs-category-tab-id-1').addClass('active show');
    $('#tabs-home').removeClass('active show');
    $('a[href="#tabs-category-tab-id-1"]').addClass('active');
    $('a[href="#tabs-home"]').removeClass('active');
}

