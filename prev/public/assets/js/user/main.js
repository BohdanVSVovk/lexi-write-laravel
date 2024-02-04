"use strict";



// profile image
function preview() {
    frame.src=URL.createObjectURL(event.target.files[0]);
}

    // copy text input
function clipboard(elem, event) {
    elem.prev('input[type="text"]').focus().select();
    document.execCommand(event);
    elem.prev('input[type="text"]').blur();
    elem.addClass('clicked');
    $('.copy-text').addClass('hidden');
  }
  $('.btn-copy').on('click', function(){
    clipboard($(this), 'copy')
  });



//   radio button click
  $(window).on('load', function() {
      if($("#radio-option-1").prop("checked")){
        $("#twoCarDiv").show();
        $("#threeCarDiv").hide();
    }
    $("input[name$='cards']").on('click', function() {
        var test = $(this).val();
        $(".package-description").hide();
        $("#" + test).show();
    });
});
//
$(".profile-back").on('click', function() {
    $("#account-sidebar").removeClass("hidden md:block");
    $(".main-profile-content").addClass("hidden md:block")
})
$(".tooltip-edit").tooltip({
  position: {
      at: "right-38 bottom-5 width-22"
  },
  tooltipClass: "toolips-edit-width"
});
$(".tooltip-delete").tooltip({
  position: {
      at: "right-41 bottom-5"
  },
  tooltipClass: "toolips-delete-width"
});

$('.cancel-tooltip').tooltip({
    position: {
        at: "left-0 bottom-5"
    },
    tooltipClass: "toolips-cancel-width"
})

$(document).ready(function() {
  $('.table-dropdown-click').on('click', function() {
      var dropdownContent = $(this).next('.table-drop-body');

      // Check if the current dropdown is already open
      if (dropdownContent.is(':visible')) {
          dropdownContent.slideUp(); // Close the dropdown with slideUp animation
      } else {
          // Close other open dropdowns
          $('.table-drop-body').slideUp();

          // Toggle current dropdown with slideDown animation
          dropdownContent.slideDown();
      }
  });

  // Close the dropdown if the user clicks outside of it
  $(document).on('click', function(event) {
      if (!$(event.target).closest('.table-dropdown-click').length && !$(event.target).closest('.table-drop-body').length) {
          $('.table-drop-body').slideUp();
      }
      if (!$(event.target).closest('.table-dropdown-click').length && !$(event.target).closest('.table-drop-body').length && !$(event.target).hasClass('tox-tinymce-aux')) {
        $('.table-drop-body').slideUp();
    }
  });

});
$(".image-tooltip-view").tooltip({
  position: {
      at: "right-42 bottom-5"
  },
  tooltipClass: "view-image-toolips-width"
});
$(".image-tooltip-download").tooltip({
  position: {
      at: "right-56 bottom-5"
  },
  tooltipClass: "download-image-toolips-width"
});
$(".image-tooltip-delete").tooltip({
  position: {
      at: "right-46 bottom-5"
  },
  tooltipClass: "delete-image-toolips-width"
});

  var buttons = $('.payNowButton');

  buttons.each(function() {
    var button = $(this);

    if (!button.is(':disabled')) {
      button.tooltip({
        position: {
          at: "right-48 bottom-5"
      },
        tooltipClass: "payNow-tooltip"
      });
    } else {
      button.removeAttr('title');
    }
  });
  $(".bill-tooltips").tooltip({
    position: {
        at: "right-52 bottom-5"
    },
    tooltipClass: "bill-tooltip"
  });

  $(".docs-tooltip-edit").tooltip({
    position: {
        at: "right-38 bottom-5"
    },
    tooltipClass: "toolips-edit-width"
  });
  $(".docs-tooltip-delete").tooltip({
    position: {
        at: "right-46 bottom-5"
    },
    tooltipClass: "toolips-delete-width"
  });