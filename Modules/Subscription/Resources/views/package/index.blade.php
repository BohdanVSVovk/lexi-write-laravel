@extends('admin.layouts.app')
@section('page_title', __('Plan'))

@section('content')

<!-- Main content -->
<div class="col-sm-12 list-container" id="package-list-container">
    <div class="card">
        <div class="card-header d-md-flex justify-content-between align-items-center">
            <h5>{{ __('Plan') }}</h5>
            <div class="mt-md-0 mt-2">
                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#batchDelete" class="btn btn-outline-primary mb-0 custom-btn-small d-none">
                    <span class="feather icon-trash-2 me-1"></span>
                    {{ __('Batch Delete') }} (<span class="batch-delete-count">0</span>)
                </a>
                @if (in_array('Modules\Subscription\Http\Controllers\PackageController@create', $prms))
                    <a href="{{ route('package.create') }}" class="btn mb-0 btn-outline-primary custom-btn-small">
                        <span class="fa fa-plus"> &nbsp;</span>{{ __('Add Plan') }}
                    </a>
                @endif
                <button class="btn btn-outline-primary mb-0 custom-btn-small me-0 collapsed filterbtn" type="button" data-bs-toggle="collapse" data-bs-target="#filterPanel" aria-expanded="true" aria-controls="filterPanel">
                    <span class="fas fa-filter">&nbsp;</span>{{ __('Filter') }}
                </button>
            </div>
        </div>

        <div class="card-header p-0 collapse" id="filterPanel">
            <div class="row mx-2 my-3">
                <div class="col-md-3">
                    <select class="select2-hide-search filter" name="status">
                        <option>{{ __('All Status') }}</option>
                        <option value="Active">{{ __('Active') }}</option>
                        <option value="Inactive">{{ __('Inactive') }}</option>
                        <option value="Pending">{{ __('Pending') }}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body px-4 product-table need-batch-operation payment-table"
                data-namespace="Modules\Subscription\Entities\Package" data-column="id">
            <div class="card-block pt-2 px-0">
                <div class="col-sm-12">
                    @include('admin.layouts.includes.yajra-data-table')
                </div>
            </div>
        </div>
        @include('admin.layouts.includes.delete-modal')
    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('public/datta-able/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('Modules/Subscription/Resources/assets/js/subscription.min.js') }}"></script>
@endsection