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
                <input type="text" class="position-relative text-dark form-control border-0 p-2 text-left f-14 f-w-500 border-additional-grey"
                    id="datatableRange" placeholder="@lang('placeholders.dateRange')">
            </div>
        </div>

        <div class="select-box d-flex  py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.employee')</p>
            <div class="select-status">
                <select class="form-control select-picker"  id="assignedTo" data-live-search="true" data-size="8">
                    <option value="all">@lang('app.all')</option>
                    @foreach ($employees as $employee)
                        <x-user-option :user="$employee" />
                    @endforeach
                </select>
            </div>
        </div>
        <div class="select-box d-flex  py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.project')</p>
            <div class="select-status">
                <select class="form-control select-picker"  id="project_id" data-live-search="true" data-size="8">
                    <option value="all">@lang('app.all')</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->project_name }}
                        </option>
                    @endforeach
                </select>

            </div>
        </div>

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
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">

        <div class="d-flex flex-column">
            <div class="d-grid d-lg-flex d-md-flex action-bar align-items-center mt-3 ">
                <div id="table-actions" class="flex-grow-1 align-items-center">
                </div>

                <div class="btn-group mt-2 mt-lg-0 mt-md-0 ml-0 ml-lg-3 ml-md-3" role="group">
                    <a href="{{ route('task-report.index') }}" class="btn btn-secondary f-14" data-toggle="tooltip"
                        data-original-title="@lang('app.menu.taskReport')"><i class="side-icon bi bi-list-ul"></i></a>

                    <a href="{{ route('employee-wise-task-report') }}" class="btn btn-secondary f-14" data-toggle="tooltip"
                        data-original-title="@lang('modules.tasks.employeeWiseTaskReport')"><i class="side-icon bi bi-people-fill"></i></a>
                        <a href="{{ route('consolidated-task-report') }}" class="btn btn-secondary f-14 btn-active" data-toggle="tooltip"
                        data-original-title="@lang('modules.tasks.consolidatedTaskReport')"><i class="side-icon bi bi-calendar-fill"></i></a>
                </div>
            </div>

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
        $('#consolidated-task-table').on('preXhr.dt', function(e, settings, data) {

        var dateRangePicker = $('#datatableRange').data('daterangepicker');
        var startDate = $('#datatableRange').val();

        if (startDate == '') {
            startDate = null;
            endDate = null;
        } else {
            startDate = dateRangePicker.startDate.format('{{ company()->moment_date_format }}');
            endDate = dateRangePicker.endDate.format('{{ company()->moment_date_format }}');
        }


        var employee = $('#employee').val();
        var project_id = $('#project_id').val();

        var assignedTo = $('#assignedTo').val();

        var status = $('#status').val();
        var searchText = $('#search-text-field').val();

        data['employee'] = employee;
        data['project_id'] = project_id;
        data['assignedTo'] = assignedTo;
        data['status'] = status;
        data['startDate'] = startDate;
        data['endDate'] = endDate;
        data['searchText'] = searchText;
        });
        const showTable = () => {
            window.LaravelDataTables["consolidated-task-table"].draw(false);
        }


        $(' #status, #field, #employee, #assignedTo, #project_id')
            .on('change keyup',
                function() {
                    if ($('#status').val() != "all") {
                        $('#reset-filters').removeClass('d-none');
                        showTable();
                    }else if ($('#employee').val() != "all") {
                        $('#reset-filters').removeClass('d-none');
                        showTable();
                    } else if ($('#assignedTo').val() != "all") {
                        $('#reset-filters').removeClass('d-none');
                        showTable();
                    } else {
                        $('#reset-filters').addClass('d-none');
                        showTable();
                    }
                });

        $('#search-text-field').on('keyup', function() {
            if ($('#search-text-field').val() != "") {
                $('#reset-filters').removeClass('d-none');
                showTable();
            }
        });

        $('#reset-filters,#reset-filters-2').click(function() {
            $('#filter-form')[0].reset();

            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
        });
    </script>
@endpush
