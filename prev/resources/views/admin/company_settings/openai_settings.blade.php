@extends('admin.layouts.app')
@section('page_title', __('Company Settings'))
@section('css')
    {{-- Media manager --}}
    <link rel="stylesheet" href="{{ asset('Modules/MediaManager/Resources/assets/css/media-manager.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dist/css/product.min.css') }}">
@endsection

@section('content')
    <!-- Main content -->
    <div class="col-sm-12" id="company-settings-container">
        <div class="card">
            <div class="card-body row">
                <div class="col-lg-3 col-12 z-index-10 pe-0 ps-0 ps-md-3">
                    @include('admin.layouts.includes.general_settings_menu')
                </div>
                <div class="col-lg-9 pl-1 pl-0">
                    <div class="card card-info shadow-none mb-0">
                        @if (session('errorMgs'))
                            <div class="alert alert-warning fade in alert-dismissable">
                                <strong>{{ __('Warning') }}!</strong> {{ session('errorMgs') }}. <a class="close" href="#"
                                    data-bs-dismiss="alert" aria-label="close" title="close">×</a>
                            </div>
                        @endif
                        <span id="smtp_head">
                            <div class="card-header p-t-20 border-bottom">
                                <h5>{{ __('OpenAI Setup') }}
                                </h5>
                            </div>
                        </span>
                        <form action="{{ route('aiSettings.option') }}" method="post" id="aiSettings"
                            class="form-horizontal">
                            <div class="d-flex justify-content-between mt-16p">
                                <div id="#headingOne">
                                    <h5 class="text-btn">{{ __('OpenAi Key') }}</h5>
                                </div>
                                <div class="mr-3"></div>
                            </div>
                            <div class="card-body p-l-15">
                                <input type="hidden" value="{{ csrf_token() }}" name="_token" id="token">
                                <div class="form-group row">
                                    <label class="col-sm-3 control-label text-left require">{{ __('OpenAi Key') }}</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                            value="{{ config('openAI.is_demo') ? 'sk-xxxxxxxxxxxxxxx' : $openai['openai'] ?? '' }}"
                                            class="form-control inputFieldDesign" name="openai" id="openai_key">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 control-label text-left">{{ __('Stable Diffusion Key') }}</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                            value="{{ config('openAI.is_demo') ? 'sk-xxxxxxxxxxxxxxx' : $openai['stablediffusion'] ?? '' }}"
                                            class="form-control inputFieldDesign" name="stablediffusion" id="stablediffusion_key">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 control-label text-left require">{{ __('Max Length for Short Description') }}</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                            value="{{ $openai['short_desc_length'] ?? '' }}"
                                            class="form-control inputFieldDesign positive-int-number" name="short_desc_length" required maxlength="191"
                                            oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 control-label text-left require">{{ __('Max Length for Long Description ') }}</label>

                                    <div class="col-sm-8">
                                        <input type="text"
                                            value="{{ $openai['long_desc_length'] ?? '' }}"
                                            class="form-control inputFieldDesign positive-int-number" name="long_desc_length" required maxlength="300"
                                            oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between pt-3">
                                <div id="#headingOne">
                                    <h5 class="text-btn">{{ __('Live Mode') }}</h5>
                                </div>
                                <div class="mr-3"></div>
                            </div>
                            <div class="card-body p-l-15">
                                <input type="hidden" value="{{ csrf_token() }}" name="_token" id="token">
                                <div class="form-group row">
                                    <label class="col-sm-3 text-left control-label">{{ __('OpenAI Model') }}</label>
                                    <div class="col-sm-8">
                                        <select class="form-control select2-hide-search inputFieldDesign" name="ai_model">
                                            @foreach($aiModels as $key => $aiModel)
                                            <option value="{{ $key }}"
                                                {{ $key == $openai['ai_model'] ? 'selected="selected"' : '' }}>
                                                {{ $aiModel }} ({{ $aiModelDescription[$key] }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!--smtp form start here-->
                                <span id="smtp_form">
                                    <div class="form-group row">
                                        <label class="col-sm-3 control-label text-left require">{{ __('Max Result Length (Token)') }}</label>
                                        <div class="col-sm-8">
                                            <input type="text"
                                                value="{{ $openai['max_token_length'] ?? $openai['max_token_length'] }}"
                                                class="form-control inputFieldDesign positive-int-number" name="max_token_length" required
                                                oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                                        </div>
                                    </div>
                                </span>
                            </div>

                            <div class="card-footer p-0">
                                <div class="form-group row">
                                    <label for="btn_save" class="col-sm-3 control-label"></label>
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn form-submit custom-btn-submit float-right" id="footer-btn">
                                            {{ __('Save') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('mediamanager::image.modal_image')

@endsection

@section('js')
    <script>
        var openai_key = "{{ $openai['openai'] ?? '' }}";
        var stablediffusion_key = "{{ $openai['stablediffusion'] ?? '' }}";
    </script>
    <script src="{{ asset('public/dist/js/custom/openai-settings.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/validation.min.js') }}"></script>
@endsection
