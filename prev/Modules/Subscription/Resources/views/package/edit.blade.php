@extends('admin.layouts.app')
@section('page_title', __('Edit :x', ['x' => __('Plan')]))
@section('css')
    <link rel="stylesheet" href="{{ asset('Modules/Subscription/Resources/assets/css/subscription.min.css') }}">
@endsection
@section('content')
    <!-- Main content -->
    <div class="col-sm-12" id="package-edit-container">
        <div class="card">
            <div class="card-body row" id="package-container">
                <div class="col-lg-3 col-12 z-index-10 pe-0 ps-0 ps-md-3" aria-labelledby="navbarDropdown">
                    <div class="card card-info shadow-none" id="nav">
                        <div class="card-header pt-4 border-bottom text-nowrap">
                            <h5 id="general-settings">{{ __('Package Edit') }}</h5>
                        </div>
                        <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <li><a class="nav-link text-left tab-name active" id="v-pills-general-tab" data-bs-toggle="pill"
                                    href="#v-pills-general" role="tab" aria-controls="v-pills-general"
                                    aria-selected="true" data-id="{{ __('General') }}">{{ __('General') }}</a></li>
                            <li><a class="nav-link text-left tab-name" id="v-pills-usecase-tab" data-bs-toggle="pill"
                                href="#v-pills-usecase" role="tab" aria-controls="v-pills-usecase"
                                aria-selected="true" data-id="{{ __('Use Case') }}">{{ __('Use Case') }}</a></li>
                            <li class="featuers mt-2 font-bold text-dark ms-3">{{ __('Features') }}</li>
                            @foreach ($features as $key => $value)
                                <li class="ms-3 {{ str_contains($key, 'custom') ? 'custom-feature-nav' : '' }}">
                                    <a class="nav-link text-left tab-name" id="v-pills-{{ $key }}-tab" data-bs-toggle="pill"
                                        href="#v-pills-{{ $key }}" role="tab" aria-controls="v-pills-{{ $key }}"
                                        aria-selected="true"
                                        data-id="{{ ucwords(str_replace('-', ' ', $key)) }}">{{ str_replace('-', ' ', $key) }}</a>
                                    @if (str_contains($key, 'custom'))
                                        <span class="close">X</span>
                                    @endif
                                </li>
                            @endforeach
                            <li class="add-feature-nav" data-count="{{ (int) filter_var($key, FILTER_SANITIZE_NUMBER_INT) + 1 }}">+ {{ __('Add Feature') }}</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9 col-12 ps-0">
                    <div class="card card-info shadow-none">
                        <div class="card-header pt-4 border-bottom">
                            <h5><span id="theme-title">{{ __('General') }}</span></h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('package.update', ['id' => $package->id]) }}" method="post">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                                <div class="tab-content p-0 box-shadow-unset" id="topNav-v-pills-tabContent">
                                    {{-- General --}}
                                    <div class="tab-pane fade active show" id="v-pills-general" role="tabpanel"
                                        aria-labelledby="v-pills-general-tab">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group row">
                                                    <div class="col-md-6">
                                                        <label for="name" class="control-label require">{{ __('Name') }}</label>
                                                        <input type="text" placeholder="{{ __('Name') }}"
                                                            class="form-control form-width inputFieldDesign" id="name"
                                                            name="name" required minlength="3" value="{{ old('name', $package->name) }}"
                                                            oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                                            data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Name'), 'y' => 3]) }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="code" class="control-label require">{{ __('Code') }}</label>
                                                        <input type="text" placeholder="{{ __('Code') }}"
                                                            class="form-control form-width inputFieldDesign" id="code"
                                                            name="code" required minlength="3" value="{{ old('code', $package->code) }}"
                                                            oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                                            data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Code'), 'y' => 3]) }}">
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <div class="col-md-6">
                                                        <label for="sale_price" class="control-label require">{{ __('Sale Price') }}</label>
                                                        <input type="text" placeholder="{{ __('Sale Price') }}"
                                                            class="form-control form-width inputFieldDesign positive-float-number" id="sale_price"
                                                            name="sale_price" value="{{ formatCurrencyAmount(old('sale_price', $package->sale_price)) }}"
                                                            required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="discount_price" class="control-label">{{ __('Discount Price') }}</label>
                                                        <input type="text" placeholder="{{ __('Discount Price') }}"
                                                            class="form-control form-width inputFieldDesign positive-float-number" id="discount_price"
                                                            name="discount_price" value="{{ formatCurrencyAmount(old('discount_price', $package->discount_price)) }}">
                                                    </div>
                                                </div>
                                                <input type="hidden" name="short_description" value="">

                                                <div class="form-group row">
                                                    <div class="col-md-6">
                                                        <label for="sort_order" class="control-label">{{ __('Sort') }}</label>
                                                        <input type="text" placeholder="{{ __('Sort') }}"
                                                            class="form-control form-width inputFieldDesign positive-int-number" id="sort_order"
                                                            name="sort_order" value="{{ old('sort_order', $package->sort_order) }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="trial_day" class="control-label">{{ __('Trial Day') }}</label>
                                                        <input type="text" placeholder="{{ __('Trial Day') }}"
                                                            class="form-control form-width inputFieldDesign positive-int-number" id="trial_day"
                                                            name="trial_day" value="{{ old('trial_day', $package->trial_day) }}">
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <div class="col-sm-4">
                                                        <label for="billing_cycle" class="control-label">{{ __('Billing Cycle') }}</label>
                                                        <select class="form-control select2-hide-search inputFieldDesign"
                                                            name="billing_cycle" id="billing_cycle">
                                                            <option value="days" @selected(old('billing_cycle', $package->billing_cycle) == 'days')>{{ __('Days') }}</option>
                                                            <option value="weekly" @selected(old('billing_cycle', $package->billing_cycle) == 'weekly')>{{ __('Weekly') }}</option>
                                                            <option value="monthly" @selected(old('billing_cycle', $package->billing_cycle) == 'monthly')>{{ __('Monthly') }}</option>
                                                            <option value="yearly" @selected(old('billing_cycle', $package->billing_cycle) == 'yearly')>{{ __('Yearly') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 {{ $package->billing_cycle == 'days' ? '' : 'd-none' }}" id="duration_days">
                                                        <label for="duration" class="control-label">{{ __('Duration') }}</label>
                                                        <input type="text" placeholder="{{ __('Days') }}"
                                                            class="form-control form-width inputFieldDesign positive-int-number" id="duration"
                                                            name="meta[0][duration]" value="{{ $package->duration }}">
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label for="renewable" class="control-label">{{ __('Renewable') }}</label>
                                                        <select class="form-control select2-hide-search inputFieldDesign"
                                                            name="renewable" id="renewable">
                                                            <option value="0"
                                                                {{ old('renewable', $package->renewable) == '0' ? 'selected' : '' }}>{{ __('No') }}</option>
                                                            <option value="1"
                                                                {{ old('renewable', $package->renewable) == '1' ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label for="status" class="control-label">{{ __('Status') }}</label>
                                                        <select class="form-control select2-hide-search inputFieldDesign"
                                                            name="status" id="package_status">
                                                            <option value="Active"
                                                                {{ old('status', $package->status) == 'Active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                            <option value="Inactive"
                                                                {{ old('status', $package->status) == 'Inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                                            <option value="Pending"
                                                                {{ old('status', $package->status) == 'Pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Use Case --}}
                                    <div class="tab-pane fade" id="v-pills-usecase" role="tabpanel"
                                        aria-labelledby="v-pills-usecase-tab">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="usecase-category" class="control-label require">{{ __('Use Case Category') }}</label>
                                                        <select class="form-control select2 inputFieldDesign"
                                                            name="meta[0][usecaseCategory][]" id="usecase-category" multiple required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                                                            @foreach ($useCaseCategory as $category)
                                                                <option value="{{ $category->id }}" {{ in_array($category->id, json_decode($package->usecaseCategory) ?? []) ? 'selected' : '' }}>{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="usecase-template" class="control-label require">{{ __('Use Case Template') }}</label>
                                                        <select class="form-control select2 inputFieldDesign"
                                                            name="meta[0][usecaseTemplate][]" id="usecase-template" multiple required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                                                            @foreach ($useCaseTemplate as $template)
                                                                <option value="{{ $template->slug }}" {{ in_array($template->slug, json_decode($package->usecaseTemplate) ?? []) ? 'selected' : '' }}>{{ $template->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Features --}}
                                    @foreach ($features as $key => $feature)
                                        <div class="tab-pane fade" id="v-pills-{{ $key }}" role="tabpanel"
                                            aria-labelledby="v-pills-{{ $key }}-tab">
                                            <input type="hidden" name="meta[{{ $key }}][type]" value="{{ $feature->type }}">
                                            <input type="hidden" name="meta[{{ $key }}][is_value_fixed]" value="{{ $feature->is_value_fixed }}">
                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <label for="title" class="control-label {{ isset($feature->value) ? 'require' : '' }}">{{ __('Title') }}</label>
                                                    <input type="text" placeholder="{{ __('Title') }}" {{ isset($feature->value) ? 'required' : '' }}
                                                        class="form-control form-width inputFieldDesign" id="title"
                                                        name="meta[{{ $key }}][title]" value="{{ $feature->title }}"
                                                        oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                                                </div>
                                                @if ($feature->type == 'number')
                                                    <div class="col-sm-6">
                                                        <label for="title_position" class="control-label">{{ __('Position') }}</label>
                                                        <select class="form-control select2-hide-search inputFieldDesign"
                                                            name="meta[{{ $key }}][title_position]" id="{{ $key }}title_position">
                                                            <option value="before"
                                                                {{ $feature->title_position == 'before' ? 'selected' : '' }}>{{ __('Before the value') }}</option>
                                                            <option value="after"
                                                                {{ $feature->title_position == 'after' ? 'selected' : '' }}>{{ __('After the value') }}</option>
                                                        </select>
                                                    </div>
                                                @endif
                                            </div>
                                            @if ($feature->type <> 'string')
                                                <div class="form-group row">
                                                    @if ($feature->title == 'Max Image Resolution')
                                                        <div class="col-sm-6">
                                                            <label for="value" class="control-label">{{ __('Value') }}</label>
                                                            <select class="form-control select2-hide-search inputFieldDesign"
                                                                name="meta[{{ $key }}][value]" id="{{ $key }}value">
                                                                @foreach (processPreferenceData($meta->resulation) as $value)
                                                                    <option value="{{ explode('x', $value)[0] }}"
                                                                        {{ old('value', $feature->value ?? '') == explode('x', $value)[0] ? 'selected' : '' }}>{{ $value }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @elseif ($feature->type == 'number')
                                                        <div class="col-md-6">
                                                            <label for="value" class="control-label">{{ __('Value') }}</label>
                                                            <input type="text" placeholder="{{ __('Value') }}"
                                                                class="form-control form-width inputFieldDesign int-number" id="value"
                                                                name="meta[{{ $key }}][value]" value="{{ $feature->value }}">
                                                            <label class="mt-1"><span class="badge badge-warning me-2">{{ __('Note') }}</span>{{ __('-1 for unlimited') }}</label>
                                                        </div>
                                                    @elseif ($feature->type == 'bool')
                                                        <div class="col-sm-6">
                                                            <label for="value" class="control-label">{{ __('Value') }}</label>
                                                            <select class="form-control select2-hide-search inputFieldDesign"
                                                                name="meta[{{ $key }}][value]" id="{{ $key }}value">
                                                                <option value="1"
                                                                    {{ $meta->value == '1' ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                                <option value="0"
                                                                    {{ $meta->value == '0' ? 'selected' : '' }}>{{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label for="is_visible" class="control-label">{{ __('Is Visible?') }}</label>
                                                    <select class="form-control select2-hide-search inputFieldDesign"
                                                        name="meta[{{ $key }}][is_visible]" id="{{ $key }}is_visible">
                                                        <option value="1"
                                                            {{ $feature->is_visible == '1' ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                        <option value="0"
                                                            {{ $feature->is_visible == '0' ? 'selected' : '' }}>{{ __('No') }}</option>
                                                    </select>
                                                    <label class="mt-1"><span class="badge badge-warning me-2">{{ __('Note') }}</span>{{ __('This option is applicable only for the plan details section') }}</label>
                                                </div>
                                            </div>
                                            <input type="hidden" name="meta[{{ $key }}][description]" value="">

                                            <input type="hidden" name="meta[{{ $key }}][status]" value="Active">

                                        </div>
                                    @endforeach
                                </div>
                                <div class="footer py-0">
                                    <div class="form-group row">
                                        <label for="btn_save" class="col-sm-3 control-label"></label>
                                        <div class="m-auto">
                                            <button type="submit"
                                                class="btn form-submit custom-btn-submit float-right package-submit-button"
                                                id="footer-btn">{{ __('Save') }}</button>
                                            <a href="{{ route('package.index') }}"
                                                class="py-2 me-2 form-submit custom-btn-cancel float-right all-cancel-btn">{{ __('Cancel') }}</a>
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
    @include('subscription::package.add-feature')
@endsection

@section('js')
    <script>
        var dynamic_page = ['usecase', 'word', 'image', 'image-resolution'];
    </script>
    <script src="{{ asset('public/dist/js/custom/validation.min.js') }}"></script>
    <script src="{{ asset('Modules/Subscription/Resources/assets/js/subscription.min.js') }}"></script>
@endsection
