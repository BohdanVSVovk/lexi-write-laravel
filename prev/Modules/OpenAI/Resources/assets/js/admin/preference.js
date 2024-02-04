'use strict';

$(function () {
    var pagination = ['v-pills-setup-tab', 'v-pills-document-tab', 'v-pills-code-tab', 'v-pills-image-tab', 'v-pills-bot-tab'];

    if (typeof dynamic_page !== 'undefined') {
        pagination = ['v-pills-setup-tab'];
        for (const value of dynamic_page) {
            pagination.push(`v-pills-${value}-tab`)
        }
    }

    function tabTitle(id) {
        var title = $('#' + id).attr('data-id');
        $('#theme-title').html(title);
    }

    $(document).on("click", '.tab-name', function () {
        var id = $(this).attr('data-id');
        $('#theme-title').html(id);
    });

    $(document).on('click', 'button.switch-tab', function (e) {
        $('#' + $(this).attr('data-id')).tab('show');
        var titleName = $(this).attr('data-id');

        tabTitle(titleName);

        $('.tab-pane[aria-labelledby="home-tab"').addClass('show active')
        $('#' + $(this).attr('id')).addClass('active').attr('aria-selected', true)
    })

    $(".package-submit-button, .package-feature-submit-button").on("click", function () {
        setTimeout(() => {
            for (const data of pagination) {
                if ($('#' + data.replace('-tab', '')).find(".error").length) {
                    var target = $('#' + data.replace('-tab', '')).attr("aria-labelledby");
                    $('#' + target).tab('show');
                    tabTitle(target);
                    break;
                }
            }
        }, 100);
    });

})