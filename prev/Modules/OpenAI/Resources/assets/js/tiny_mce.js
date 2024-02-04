"use strict";

// tiny mce plugin customization
tinymce.init({
    selector: "textarea#basic-example",
    statusbar: false,
    menubar:false,
    promotion:false,
    contextmenu:false,
    content_style:"body{color:#898989}",
    toolbar: false,
    plugins: [
      'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
      'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
      'insertdatetime', 'media', 'table'
    ],
    toolbar: 'bold italic backcolor | alignleft aligncenter ' + 'alignright alignjustify | bullist numlist outdent indent | ' +'undo redo | blocks forecolor | ' +
    'removeformat | '
  });
