@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')

    <x-filters.filter-box>
         <!-- DATE START -->
         <div class="select-box d-flex pr-2 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.duration')</p>
            <div class="select-status d-flex">
                <input type="text" class="position-relative text-dark form-control my-2 text-left f-14  p-1 border-additional-grey"
                    id="datatableRange" placeholder="@lang('placeholders.dateRange')">
            </div>
        </div>
        <!-- DATE END -->

        <!-- SEARCH BY TASK START -->
        <div class="task-search d-flex py-1 pr-lg-3 px-lg-2 px-md-2 px-0 border-right-grey align-items-center">
            <form class="w-100 mr-1 mr-lg-0 mr-md-1 ml-md-1 ml-0 ml-lg-0">
                <div class="input-group bg-grey rounded">
                    <div class="input-group-prepend">
                        <span class="input-group-text border-0 bg-additional-grey">
                            <i class="fa fa-search f-13 text-dark-grey"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control f-14 p-1 border-additional-grey" id="search-text-field"
                           placeholder="@lang('app.startTyping')">
                </div>
            </form>
        </div>
        <!-- SEARCH BY TASK END -->

        <!-- RESET START -->
        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
        <!-- RESET END -->

        <!-- MORE FILTERS START -->
        <x-filters.more-filter-box>

            <div class="more-filter-items">
                <label class="f-14 text-dark-grey mb-12 text-capitalize"
                       for="packagFilter">@lang('superadmin.package')</label>
                <div class="select-filter mb-4">
                    <div class="select-others">
                        <select class="form-control select-picker" id="packagFilter" data-live-search="true"
                                data-container="body" data-size="8">
                            <option value="all">@lang('app.all')</option>
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}">{{ $package->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="more-filter-items">
                <label
                    class="f-14 text-dark-grey mb-12 text-capitalize">@lang('superadmin.package') @lang('modules.invoices.type')</label>
                <div class="select-filter mb-4">
                    <div class="select-others">
                        <select class="form-control select-picker" id="type" data-style="form-control">
                            <option value="all">@lang('app.all')</option>
                            <option value="monthly">@lang('app.monthly')</option>
                            <option value="annual">@lang('app.annually')</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="more-filter-items">
                <label
                    class="f-14 text-dark-grey mb-12 text-capitalize">@lang('app.status')</label>
                <div class="select-filter mb-4">
                    <div class="select-others">
                        <select class="form-control select-picker" id="company-status" data-style="form-control">
                            <option value="all">@lang('app.all')</option>
                            <option value="active">@lang('app.active')</option>
                            <option value="inactive">@lang('app.inactive')</option>
                            <option value="license_expired">@lang('superadmin.dashboard.licenseExpired')</option>
                        </select>
                    </div>
                </div>
            </div>

            @if (global_setting()->company_need_approval)

                <div class="more-filter-items">
                    <label
                        class="f-14 text-dark-grey mb-12 text-capitalize">@lang('app.approved')</label>
                    <div class="select-filter mb-4">
                        <div class="select-others">
                            <select class="form-control select-picker" id="approved-status" data-style="form-control">
                                <option value="all">@lang('app.all')</option>
                                <option value="1">@lang('app.yes')</option>
                                <option value="0">@lang('app.no')</option>
                            </select>
                        </div>
                    </div>
                </div>
            @endif

        </x-filters.more-filter-box>
        <!-- MORE FILTERS END -->
    </x-filters.filter-box>

@endsection

@section('content')
@php
$addCompanyPermission = user()->permission('add_companies');
$deleteCompanyPermission = user()->permission('delete_companies');
@endphp
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">

        <!-- Add Task Export Buttons Start -->
        <div class="d-block d-lg-flex d-md-flex justify-content-between action-bar dd">
            <div id="table-actions" class="flex-grow-1 align-items-center">
                @if($addCompanyPermission == 'all')
                <x-forms.link-primary :link="route('superadmin.companies.create')"
                    class="mr-3 openRightModal float-left mb-2 mb-lg-0 mb-md-0" icon="plus">
                    @lang('superadmin.addCompany')
                </x-forms.link-primary>
                @endif
            </div>

            @if (global_setting()->company_need_approval)
                <div class="btn-group ml-0 ml-lg-3 ml-md-3" role="group">
                    <a href="{{ route('superadmin.companies.index') }}" class="btn btn-secondary f-14 btn-active show-clients">@lang('app.showAll')</a>

                    <a href="javascript:;" class="btn btn-secondary f-14 show-unverified">
                        @if ($unapprovedCount > 0)
                            <span class="badge badge-primary">{{ $unapprovedCount }}</span>
                        @endif
                        @lang('superadmin.companies.unapproved')
                    </a>
                </div>
            @endif

        </div>
        <!-- Add Task Export Buttons End -->

        <!-- Task Box Start -->
        <div class="d-flex flex-column w-tables rounded mt-3 bg-white">

            {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}

        </div>
        <!-- Task Box End -->
    </div>
    <!-- CONTENT WRAPPER END -->

@endsection

@push('scripts')

    @include('sections.datatable_js')

    <script>
        $('#datatableRange').on('apply.daterangepicker', (event, picker) => {
            cb(picker.startDate, picker.endDate);
            $('#datatableRange').val(picker.startDate.format('{{ companyOrGlobalSetting()->moment_date_format }}') +
                ' @lang("app.to") ' + picker.endDate.format(
                    '{{ companyOrGlobalSetting()->moment_date_format }}'));
        });

        $('#datatableRange2').on('apply.daterangepicker', (event, picker) => {
            cb(picker.startDate, picker.endDate);
            $('#datatableRange2').val(picker.startDate.format('{{ companyOrGlobalSetting()->moment_date_format }}') +
                ' @lang("app.to") ' + picker.endDate.format(
                    '{{ companyOrGlobalSetting()->moment_date_format }}'));
        });

        function cb(start, end) {
            $('#datatableRange, #datatableRange2').val(start.format('{{ companyOrGlobalSetting()->moment_date_format }}') +
                ' @lang("app.to") ' + end.format(
                    '{{ companyOrGlobalSetting()->moment_date_format }}'));
            $('#reset-filters, #reset-filters-2').removeClass('d-none');

        }
        $('#companies-table').on('preXhr.dt', function (e, settings, data) {
            var dateRangePicker = $('#datatableRange').data('daterangepicker');
            var startDate = $('#datatableRange').val();

            if (startDate == '') {
                startDate = null;
                endDate = null;
            } else {
                startDate = dateRangePicker.startDate.format('{{ global_setting()->moment_date_format }}');
                endDate = dateRangePicker.endDate.format('{{ global_setting()->moment_date_format }}');
            }

            const packageName = $('#packagFilter').val();
            const type = $('#type').val();
            const companyStatus = $('#company-status').val();
            const approveStatus = $('#approved-status').val();
            const searchText = $('#search-text-field').val();

            data['startDate'] = startDate;
            data['endDate'] = endDate;
            data['package'] = packageName;
            data['type'] = type;
            data['companyStatus'] = companyStatus;
            data['approveStatus'] = approveStatus;
            data['searchText'] = searchText;
        });
        const showTable = () => {
            window.LaravelDataTables["companies-table"].draw();
        }

        $('#packagFilter, #type, #search-text-field, #company-status, #approved-status')
            .on('change keyup', function () {
                if ($('#type').val() !== "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#packagFilter').val() !== "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#company-status').val() !== "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#approved-status').val() !== "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#search-text-field').val() !== "") {
                    $('#reset-filters').removeClass('d-none');
                } else {
                    $('#reset-filters').addClass('d-none');
                }
                showTable();
            });

        $('#reset-filters').click(function () {
            $('#filter-form')[0].reset();
            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            $('.show-clients').addClass('btn-active');
            $('.show-unverified').removeClass('btn-active');
            showTable();
        });

        $('.show-unverified').click(function() {
            $('#approved-status').val('0');

            $('#approved-status').selectpicker('refresh');
            $(this).addClass('btn-active');
            $('#reset-filters').removeClass('d-none');
            showTable();
        });

        @if($deleteCompanyPermission == 'all')
            $('body').on('click', '.delete-table-row', function () {
                const id = $(this).data('company-id');
                Swal.fire({
                    title: "@lang('messages.sweetAlertTitle')",
                    text: "@lang('messages.recoverRecord')",
                    icon: 'warning',
                    showCancelButton: true,
                    focusConfirm: false,
                    confirmButtonText: "@lang('messages.confirmDelete')",
                    cancelButtonText: "@lang('app.cancel')",
                    customClass: {
                        confirmButton: 'btn btn-primary mr-3',
                        cancelButton: 'btn btn-secondary'
                    },
                    showClass: {
                        popup: 'swal2-noanimation',
                        backdrop: 'swal2-noanimation'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('superadmin.companies.destroy', ':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            blockUI: true,
                            data: {
                                '_token': token,
                                '_method': 'DELETE'
                            },
                            success: function (response) {
                                if (response.status === "success") {
                                    showTable();
                                }
                            }
                        });
                    }
                });
            });
        @endif

        @if(module_enabled('Subdomain'))
            $('body').on('click', '.domain-params', function () {
            const company_id = $(this).data('company-id');
            const company_url = $(this).data('company-url');

            Swal.fire({
                title: "@lang('messages.sweetAlertTitle')",
                html: `You want to notify company admins about company Login URL <br> Company URL: <a  target="_blank" href="${company_url}"><b>${company_url}</b></a>`,
                icon: 'warning',
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: "Yes, Notify it!",
                cancelButtonText: "@lang('app.cancel')",
                customClass: {
                    confirmButton: 'btn btn-primary mr-3',
                    cancelButton: 'btn btn-secondary'
                },
                showClass: {
                    popup: 'swal2-noanimation',
                    backdrop: 'swal2-noanimation'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    const token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        container: '#companies-table',
                        blockUI: true,
                        url: "{{route('notify.domain')}}",
                        data: {'_token': token, 'company_id': company_id},
                    });
                }
            });

        });
        @endif

        $('.btn-group .btn-secondary').click(function() {
            $('.btn-secondary').removeClass('btn-active');
            $(this).addClass('btn-active');
        });
    </script>
@endpush
