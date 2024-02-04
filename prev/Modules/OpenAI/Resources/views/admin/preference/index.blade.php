@extends('admin.layouts.app')
@section('page_title', __('Edit :x', ['x' => __('AI Preferences')]))

@section('content')
    <!-- Main content -->
    <div class="col-sm-12" id="preference-container">
        <div class="card">
            <div class="card-body row" id="preference-container">
                <div class="col-lg-3 col-12 z-index-10 pe-0 ps-0 ps-md-3" aria-labelledby="navbarDropdown">
                    <div class="card card-info shadow-none" id="nav">
                        <div class="card-header pt-4 border-bottom text-nowrap">
                            <h5 id="general-settings">{{ __('Content Types') }}</h5>
                        </div>
                        <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <li><a class="nav-link text-left tab-name active" id="v-pills-setup-tab" data-bs-toggle="pill"
                                href="#v-pills-setup" role="tab" aria-controls="v-pills-setup"
                                aria-selected="true" data-id="{{ __('AI Setup') }}">{{ __('AI Setup') }}</a></li>
                            <li><a class="nav-link text-left tab-name" id="v-pills-document-tab" data-bs-toggle="pill"
                                    href="#v-pills-document" role="tab" aria-controls="v-pills-document"
                                    aria-selected="true" data-id="{{ __('Document') }}">{{ __('Document') }}</a></li>
                            <li><a class="nav-link text-left tab-name" id="v-pills-image-tab" data-bs-toggle="pill"
                                    href="#v-pills-image" role="tab" aria-controls="v-pills-image"
                                    aria-selected="true" data-id="{{ __('Image') }}">{{ __('Image') }}</a></li>
                            <li><a class="nav-link text-left tab-name" id="v-pills-code-tab" data-bs-toggle="pill"
                                href="#v-pills-code" role="tab" aria-controls="v-pills-code"
                                aria-selected="true" data-id="{{ __('Code') }}">{{ __('Code') }}</a></li>
                            <li><a class="nav-link text-left tab-name" id="v-pills-bot-tab" data-bs-toggle="pill"
                                href="#v-pills-bot" role="tab" aria-controls="v-pills-code"
                                aria-selected="true" data-id="{{ __('Chat Bot') }}">{{ __('Chat Bot') }}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9 col-12 ps-0">
                    <div class="card card-info shadow-none">
                        <div class="card-header pt-4 border-bottom">
                            <h5><span id="theme-title">{{ __('Document') }}</span></h5>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('admin.features.preferences.create') }}" id="aiSettings">
                                @csrf

                                <div class="tab-content p-0 box-shadow-unset" id="topNav-v-pills-tabContent">
                                    {{-- OpenAI Setup --}}
                                    <div class="tab-pane fade active show" id="v-pills-setup" role="tabpanel" aria-labelledby="v-pills-setup-tab">
                                        <div class="row">
                                            <div class="d-flex justify-content-between mt-16p">
                                                <div id="#headingOne">
                                                    <h5 class="text-btn">{{ __('Ai Key') }}</h5>
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
                                                            class="form-control inputFieldDesign positive-int-number" name="short_desc_length" required pattern="^(?:[1-9]|[1-9][0-9]{1,2}|1000)$"
                                                            oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-pattern="{{ __('Value exceeds the maximum limit of 1000.') }}">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-3 control-label text-left require">{{ __('Max Length for Long Description ') }}</label>
                
                                                    <div class="col-sm-8">
                                                        <input type="text"
                                                            value="{{ $openai['long_desc_length'] ?? '' }}"
                                                            class="form-control inputFieldDesign positive-int-number" name="long_desc_length" required  pattern="^(?:[1-9]|[1-9][0-9]{1,2}|1000)$"
                                                            oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-pattern="{{ __('Value exceeds the maximum limit of 1000.') }}">
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
                                        </div>
                                    </div>
                                    {{-- Document --}}
                                    <div class="tab-pane fade" id="v-pills-document" role="tabpanel" aria-labelledby="v-pills-document-tab">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label require">{{ __('Select Languages') }}</label>
                                                        <select class="form-control select2 inputFieldDesign sl_common_bx"
                                                            name="meta[document][language][]" id="document_language" multiple required>
                                                            @foreach ($languages as $language)
                                                                @if ( !array_key_exists($language->name, $omitLanguages) )
                                                                <option value="{{ $language->name }}"
                                                                    {{ in_array($language->name, processPreferenceData($meta['document'][0]->value ?? NULL) ) ? 'selected' : '' }}> {{ $language->name }} </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label require">{{ __('Select Tones') }}</label>
                                                        <select class="form-control select2 inputFieldDesign sl_common_bx"
                                                            name="meta[document][tone][]" multiple required>
                                                            @foreach ($preferences['document']['tone'] as $key => $tone)
                                                                <option value="{{ $key }}" {{ in_array($tone, processPreferenceData($meta['document'][1]->value ?? NULL)) ? 'selected' : '' }} > {{ $tone }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label require">{{ __('Number of variants') }}</label>
                                                        <select class="form-control select2 inputFieldDesign sl_common_bx"
                                                            name="meta[document][variant][]" multiple required>
                                                            @foreach ($preferences['document']['variant'] as $key => $variant)
                                                                <option value="{{ $key }}" {{ in_array($variant, processPreferenceData($meta['document'][2]->value ?? NULL)) ? 'selected' : '' }} > {{ $variant }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label require">{{ __('Creativity Level') }}</label>
                                                        <select class="form-control select2 inputFieldDesign sl_common_bx"
                                                            name="meta[document][temperature][]" multiple required>
                                                            @foreach ($preferences['document']['temperature'] as $key => $temperature)
                                                                <option value="{{ $key }}" {{ in_array($temperature, processPreferenceData($meta['document'][3]->value ?? NULL)) ? 'selected' : '' }} > {{ $temperature }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-image" role="tabpanel" aria-labelledby="v-pills-image-tab">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label require">{{ __('Image Create Using') }}</label>
                                                        <select class="form-control select2 inputFieldDesign sl_common_bx"
                                                            name="meta[image_maker][imageCreateFrom][]" required>

                                                            @foreach ($preferences['image_maker']['imageCreateFrom'] as $key => $image)
                                                                <option value="{{ $key }}" {{ in_array($key, processPreferenceData($meta['image_maker'][4]->value ?? NULL)) ? 'selected' : '' }} > {{ $image }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label require">{{ __('Number of variants') }}</label>
                                                        <select class="form-control select2 inputFieldDesign sl_common_bx"
                                                            name="meta[image_maker][variant][]" multiple required>
                                                            @foreach ($preferences['image_maker']['variant'] as $key => $variant)
                                                                <option value="{{ $key }}" {{ in_array($variant, processPreferenceData($meta['image_maker'][0]->value ?? NULL)) ? 'selected' : '' }} > {{ $variant }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label require">{{ __('Resulation') }}</label>
                                                        <select class="form-control select2 inputFieldDesign sl_common_bx"
                                                            name="meta[image_maker][resulation][]" multiple required>
                                                            @foreach ($preferences['image_maker']['resulation'] as $key => $resulation)
                                                                <option value="{{ $key }}" {{ in_array($resulation, processPreferenceData($meta['image_maker'][1]->value ?? NULL)) ? 'selected' : '' }} > {{ $resulation }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label require">{{ __('Image Style') }}</label>
                                                        <select class="form-control select2 inputFieldDesign sl_common_bx"
                                                            name="meta[image_maker][artStyle][]" multiple required>
                                                            @foreach ($preferences['image_maker']['artStyle'] as $key => $artStyle)
                                                                <option value="{{ $key }}" {{ in_array($artStyle, processPreferenceData($meta['image_maker'][2]->value ?? NULL)) ? 'selected' : '' }} > {{ $artStyle }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label require">{{ __('Lighting Effects') }}</label>
                                                        <select class="form-control select2 inputFieldDesign sl_common_bx"
                                                            name="meta[image_maker][lightingStyle][]" multiple required>
                                                            @foreach ($preferences['image_maker']['lightingStyle'] as $key => $lightingStyle)
                                                                <option value="{{ $key }}" {{ in_array($lightingStyle, processPreferenceData($meta['image_maker'][3]->value ?? NULL)) ? 'selected' : '' }} > {{ $lightingStyle }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-code" role="tabpanel" aria-labelledby="v-pills-code-tab">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label require">{{ __('Language') }}</label>
                                                        <select class="form-control select2 inputFieldDesign sl_common_bx"
                                                            name="meta[code_writer][language][]" multiple required>
                                                            @foreach ($preferences['code_writer']['language'] as $key => $language)
                                                                <option value="{{ $key }}" {{ in_array($language, processPreferenceData($meta['code_writer'][0]->value ?? NULL)) ? 'selected' : '' }} > {{ $language }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label require">{{ __('Code Level') }}</label>
                                                        <select class="form-control select2 inputFieldDesign sl_common_bx"
                                                            name="meta[code_writer][codeLabel][]" multiple required>
                                                            @foreach ($preferences['code_writer']['codeLabel'] as $key => $codeLabel)
                                                                <option value="{{ $key }}" {{ in_array($codeLabel, processPreferenceData($meta['code_writer'][1]->value ?? NULL)) ? 'selected' : '' }} > {{ $codeLabel }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-bot" role="tabpanel" aria-labelledby="v-pills-bot-tab">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label require">{{ __('Name') }}</label>
                                                        <div class="col-sm-12">
                                                            <input type="text" placeholder="{{ __('Name') }}"
                                                                class="form-control form-width inputFieldDesign" id="name"
                                                                name="name" required minlength="3" value="{{ old('name', $chatBot->name) }}"
                                                                oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                                                data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Name'), 'y' => 3]) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row preview-parent">
                                                    <label class="col-sm-12 form-label">{{ __('Image') }}</label>
                                                    <div class="col-sm-12">
                                                        <div class="custom-file position-relative media-manager-img" data-val="single"
                                                            data-returntype="ids" id="image-status">
                                                            <input class="custom-file-input is-image form-control inputFieldDesign"
                                                                name="custom_file_input">
                                                            <label class="custom-file-label overflow_hidden d-flex align-items-center"
                                                                for="validatedCustomFile">{{ __('Upload image') }}</label>
                                                        </div>
                                                        <div class="preview-image" id="#">
                                                            <!-- img will be shown here -->
                                                            @if (!empty($chatBot->objectFile))
                                                            <div class="d-flex flex-wrap mt-2">
                                                                <div class="position-relative border boder-1 p-1 mr-2 rounded mt-2">
                                                                <div class="position-absolute rounded-circle text-center img-remove-icon">
                                                                                <i class="fa fa-times"></i>
                                                                </div>
                                                                    <img class="upl-img p-1" src="{{ $chatBot->fileUrl() }}">
                                                                    <input type="hidden" value="{{ $chatBot->objectFile->file_id }}" name="file_id[]">
                                                                </div>
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="footer py-0">
                                    <div class="form-group row">
                                        <label for="btn_save" class="col-sm-3 control-label"></label>
                                        <div class="m-auto">
                                            <button type="submit"
                                                class="btn form-submit custom-btn-submit float-right package-submit-button"
                                                id="footer-btn">{{ __('Save') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
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
    <script src="{{ asset('Modules/OpenAI/Resources/assets/js/admin/preference.min.js') }}"></script>
@endsection

