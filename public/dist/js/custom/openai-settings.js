"use strict";
$(document).ready(function() {
    $('.image-class').val() == 'Stablediffusion' ? $('.stable-diffusion-model').show() : $('.stable-diffusion-model').hide();
});

$(document).on("keyup", "#aiSettings", function(){

    if( $('#openai_key').val() === "" && openai_key != "" ) {
        $('#openai_key').prop('required',true);
        $('#openai_key').attr('oninvalid',"this.setCustomValidity('{{ __('This field is required.') }}')");
    }

    if( $('#stablediffusion_key').val() === "" && stablediffusion_key != "" ) {
        $('#stablediffusion_key').prop('required',true);
        $('#stablediffusion_key').attr('oninvalid',"this.setCustomValidity('{{ __('This field is required.') }}')");
    }

    if( $('#googleApi_key').val() === "" && googleApi_key != "" ) {
        $('#googleApi_key').prop('required',true);
        $('#googleApi_key').attr('oninvalid',"this.setCustomValidity('{{ __('This field is required.') }}')");
    }
    
});

$(document).on('change', '.image-class', function () {
    this.value == 'Stablediffusion' ? $('.stable-diffusion-model').show() : $('.stable-diffusion-model').hide();
})