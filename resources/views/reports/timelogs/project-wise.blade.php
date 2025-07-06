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
                <input type="text"
                    class="position-relative text-dark form-control border-0 p-2 text-left f-14 f-w-500 border-additional-grey"
                    id="datatableRange2" placeholder="@lang('placeholders.dateRange')">
            </div>
        </div>
        <!-- DATE END -->

        <!-- EMPLOYEE START -->
        <div class="select-box d-flex  py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.employee')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="employee" id="employee" data-live-search="true"
                    data-size="8">
                    <option value="all">@lang('app.all')</option>
                    @foreach ($employees as $employee)
                        <x-user-option :user="$employee" />
                    @endforeach
                </select>
            </div>
        </div>
        <!-- EMPLOYEE END -->

        <!-- PROJECT START -->
        <div class="select-box d-flex  py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.project')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="project" id="project" data-live-search="true"
                    data-size="8">
                    <option value="all">@lang('app.all')</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <!-- PROJECT END -->

        <!-- RESET START -->
        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
        <!-- RESET END -->
    </x-filters.filter-box>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="d-grid d-lg-flex d-md-flex action-bar mb-4">
            <div id="table-actions" class="flex-grow-1 align-items-center">
                @if (canDataTableExport())
                    <x-forms.button-secondary id="export-by-project" class="mr-3 mb-2 mb-lg-0" icon="file-export">
                        @lang('app.exportExcel')
                    </x-forms.button-secondary>
                @endif
            </div>

            <!-- Tabs -->
            <div class="btn-group mt-2 mt-lg-0 mt-md-0 ml-0 ml-lg-3 ml-md-3" role="group">
                <div class="btn-group mt-2 mt-lg-0 mt-md-0 ml-auto" role="group">
                    <a href="{{ route('time-log-report.index') }}" class="btn btn-secondary f-14" data-toggle="tooltip"
                        data-original-title="@lang('app.menu.timeLogReport')"><i class="side-icon bi bi-list-ul"></i></a>

                    <a href="{{ route('time-log-consolidated.report') }}" class="btn btn-secondary f-14" data-toggle="tooltip"
                        data-original-title="@lang('app.timelogConsolidatedReport')"><i class="side-icon bi bi-clipboard-data"></i></a>

                    <a href="{{ route('project-wise-timelog.report') }}" class="btn btn-secondary f-14 btn-active"
                        data-toggle="tooltip" data-original-title="@lang('app.projectWiseTimeLogReport')"><i class="side-icon bi bi-list"></i></a>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="d-flex flex-column w-tables rounded mt-4 bg-white">
            {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}
        </div>
    </div>
@endsection

@push('scripts')
    @include('sections.datatable_js')

    <script src="{{ asset('vendor/jquery/daterangepicker.min.js') }}"></script>
    <script>
        $(function() {
            function setDate() {
                var start = moment().clone().startOf('month');
                var end = moment();

                $('#datatableRange2').daterangepicker({
                    locale: daterangeLocale,
                    linkedCalendars: false,
                    startDate: start,
                    endDate: end,
                    ranges: daterangeConfig
                });
            }

            setDate();

            $('#timelog-project-wise-table').on('preXhr.dt', function(e, settings, data) {

                var dateRangePicker = $('#datatableRange2').data('daterangepicker');
                var startDate = $('#datatableRange2').val();

                if (startDate == '') {
                    startDate = null;
                    endDate = null;
                } else {
                    startDate = dateRangePicker.startDate.format('{{ company()->moment_date_format }}');
                    endDate = dateRangePicker.endDate.format('{{ company()->moment_date_format }}');
                }

                var employee = $('#employee').val();
                var project = $('#project').val();

                data['startDate'] = startDate;
                data['endDate'] = endDate;
                data['employee'] = employee;
                data['project'] = project;
            });

            const showTable = () => {
                window.LaravelDataTables["timelog-project-wise-table"].draw(true);
            }

            $('#datatableRange2').on('apply.daterangepicker', function(ev, picker) {
                $('#reset-filters').removeClass('d-none');
                showTable();
            });

            $('#employee, #project').on('change keyup',
            function() {
                if ($('#employee').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                    showTable();
                }
                else if ($('#project').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                    showTable();
                }
                else {
                    $('#reset-filters').addClass('d-none');
                    showTable();
                }
            });

            $('#reset-filters').click(function() {
                $('#filter-form')[0].reset();

                $('.filter-box .select-picker').selectpicker("refresh");
                $('#reset-filters').addClass('d-none');
                showTable();
            });

            $('#export-by-project').click(function() {
                var startDate = $('#datatableRange2').val();
                var endDate = '';

                if (startDate == '') {
                    startDate = null;
                    endDate = null;
                } else {
                    var dateRangePicker = $('#datatableRange2').data('daterangepicker');
                    startDate = dateRangePicker.startDate.format('{{ company()->moment_date_format }}');
                    endDate = dateRangePicker.endDate.format('{{ company()->moment_date_format }}');
                }

                var employeeID = $('#employee').val();
                var projectID = $('#project').val();
                var url = "{{ route('project-wise-timelog.export') }}";

                window.location = `${url}?startDate=${startDate}&endDate=${endDate}&employee=${employeeID}&project=${projectID}`;
            });
        });
    </script>
@endpush
