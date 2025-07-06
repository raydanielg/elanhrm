@extends('layouts.app')

@push('datatable-styles')
    <script src="{{ asset('vendor/jquery/frappe-charts.min.iife.js') }}"></script>
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
        <!-- Add Task Export Buttons Start -->
        <div class="d-flex flex-column">
           
            <div class="d-grid d-lg-flex d-md-flex action-bar align-items-center mt-4">
            <div id="table-actions" class="flex-grow-1 align-items-center">
            </div>

               <div class="btn-group mt-2 mt-lg-0 mt-md-0 ml-0 ml-lg-3 ml-md-3" role="group">
                    <a href="{{ route('time-log-weekly-report.index') }}" class="btn btn-secondary f-14 " data-toggle="tooltip"
                        data-original-title="@lang('app.menu.weeklyTimehsheet')"><i class="side-icon bi bi-list-ul"></i></a>

                    <a href="{{ route('weekly-pending-time-log-report.report') }}" class="btn btn-secondary f-14 btn-active" data-toggle="tooltip"
                        data-original-title="@lang('app.menu.weeklypPendingTimehsheet')"><i class="side-icon bi bi-clipboard-data"></i></a>
                </div>
            </div>

        </div>

        <!-- Add Task Export Buttons End -->
        <!-- Task Box Start -->
        <div class="d-flex flex-column w-tables rounded mt-4 bg-white">

            {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}

        </div>
        <!-- Task Box End -->
    </div>
    <!-- CONTENT WRAPPER END -->
@endsection

@push('scripts')
    @include('sections.datatable_js')

    <script type="text/javascript">
        function getDate() {
            // Set week to Monday (1) as start, Sunday (0) as end
            var start = moment().clone().startOf('isoWeek');
            var end = moment().clone().endOf('isoWeek');

            $('#datatableRange2').daterangepicker({
                locale: daterangeLocale,
                linkedCalendars: false,
                startDate: start,
                endDate: end,
                minDate: moment().subtract(10, 'years').startOf('isoWeek'),
                maxDate: moment().add(10, 'years').endOf('isoWeek'),
                showDropdowns: true,
                alwaysShowCalendars: true,
                ranges: {
                    'This Week': [moment().startOf('isoWeek'), moment().endOf('isoWeek')],
                    'Last Week': [moment().subtract(1, 'week').startOf('isoWeek'), moment().subtract(1, 'week').endOf('isoWeek')]
                },
                isInvalidDate: function(date) {
                    // Only allow selecting Mondays and Sundays
                    return !(date.isoWeekday() === 1 || date.isoWeekday() === 7);
                }
            }, function(start, end) {
                // If Monday selected, set range to full week (Mon-Sun)
                // If Sunday selected, set range to previous Monday to this Sunday
                if (start.isoWeekday() === 1) {
                    $('#datatableRange2').data('daterangepicker').setStartDate(start.clone().startOf('isoWeek'));
                    $('#datatableRange2').data('daterangepicker').setEndDate(start.clone().endOf('isoWeek'));
                } else if (start.isoWeekday() === 7) {
                    $('#datatableRange2').data('daterangepicker').setStartDate(start.clone().startOf('isoWeek'));
                    $('#datatableRange2').data('daterangepicker').setEndDate(start.clone().endOf('isoWeek'));
                }
            });
        }

        $(function() {
            getDate();
            $('#datatableRange2').on('apply.daterangepicker', function(ev, picker) {
                // Always set to full week (Monday to Sunday) on apply
                picker.setStartDate(picker.startDate.clone().startOf('isoWeek'));
                picker.setEndDate(picker.startDate.clone().endOf('isoWeek'));
                showTable();
            });
        });
    </script>


    <script>
        $('#timelogs-table').on('preXhr.dt', function(e, settings, data) {

            var dateRangePicker = $('#datatableRange2').data('daterangepicker');
            var startDate = $('#datatableRange2').val();

            if (startDate == '') {
                startDate = null;
                endDate = null;
            } else {
                startDate = dateRangePicker.startDate.format('{{ company()->moment_date_format }}');
                endDate = dateRangePicker.endDate.format('{{ company()->moment_date_format }}');
            }

            var projectID = $('#project_id').val();
            var employee = $('#employee').val();
            var client = $('#client').val();
            var approved = $('#status').val();
            var invoice = $('#invoice_generate').val();
            var searchText = $('#search-text-field').val();

            data['startDate'] = startDate;
            data['endDate'] = endDate;
            data['projectId'] = projectID;
            data['employee'] = employee;
            data['client'] = client;
            data['approved'] = approved;
            data['invoice'] = invoice;
            data['searchText'] = searchText;
        });
        const showTable = () => {
            window.LaravelDataTables["timelogs-table"].draw(true);
            
        }

        $('#project_id, #employee, #client, #status, #invoice_generate').on('change keyup',
            function() {
                if ($('#status').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                    showTable();
                } else if ($('#employee').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                    showTable();
                } else if ($('#client').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                    showTable();
                } else if ($('#project_id').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                    showTable();
                } else if ($('#invoice_generate').val() != "all") {
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

        $('#reset-filters').click(function() {
            $('#filter-form')[0].reset();
            // getDate()

            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
        });

        $('#reset-filters-2').click(function() {
            $('#filter-form')[0].reset();

            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
        });

    </script>
@endpush
