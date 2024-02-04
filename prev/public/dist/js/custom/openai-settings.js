"use strict";

$(document).on("keyup", "#aiSettings", function(){

    if( $('#openai_key').val() === "" && openai_key != "" ) {
        $('#openai_key').prop('required',true);
        $('#openai_key').attr('oninvalid',"this.setCustomValidity('{{ __('This field is required.') }}')");
    }

    if( $('#stablediffusion_key').val() === "" && stablediffusion_key != "" ) {
        $('#stablediffusion_key').prop('required',true);
        $('#stablediffusion_key').attr('oninvalid',"this.setCustomValidity('{{ __('This field is required.') }}')");
    }
    
});