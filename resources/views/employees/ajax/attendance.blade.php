@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@php
    $addAttendancePermission = user()->permission('add_attendance');
    $viewEmployeeTasks = user()->permission('view_employee_tasks');
    $viewEmployeeAttendance = user()->permission('view_attendance');
    $viewTickets = user()->permission('view_tickets');
    $viewEmployeeProjects = user()->permission('view_employee_projects');
    $viewEmployeeTimelogs = user()->permission('view_employee_timelogs');
    $manageEmergencyContact = user()->permission('manage_emergency_contact');
    $manageRolePermissionSetting = user()->permission('manage_role_permission_setting');
    $manageShiftPermission = user()->permission('view_shift_roster');
    $viewLeavePermission = user()->permission('view_leave');
    $viewDocumentPermission = user()->permission('view_documents');
    $viewAppreciationPermission = user()->permission('view_appreciation');
    $viewImmigrationPermission = user()->permission('view_immigration');
    $viewIncrementPermission = user()->permission('view_increment_promotion');
@endphp

@php
    $showFullProfile = false;
    $employeeDetail = $employee->employeeDetail;

    if ($viewPermission == 'all'
        || ($viewPermission == 'added' && $employeeDetail->added_by == user()->id)
        || ($viewPermission == 'owned' && $employeeDetail->user_id == user()->id)
        || ($viewPermission == 'both' && ($employeeDetail->user_id == user()->id || $employeeDetail->added_by == user()->id))
    ) {
        $showFullProfile = true;
    }
@endphp

@push('styles')
    <style>
        .attendance-total {
            width: 10%;
        }

        .table .thead-light th,
        .table tr td,
        .table h5 {
            font-size: 12px;
        }
        .mw-250{
            min-width: 125px;
        }
    </style>
@endpush

@section('filter-section')
    <div class="d-flex d-lg-block filter-box project-header bg-white">
        <div class="mobile-close-overlay w-100 h-100" id="close-client-overlay"></div>

        <div class="project-menu" id="mob-client-detail">
            <a class="d-none close-it" href="javascript:;" id="close-client-detail"><i class="fa fa-times"></i></a>

            <nav class="tabs">
                <ul class="-primary">
                    <li>
                        <x-tab :href="route('employees.show', $employee->id)" :text="__('modules.employees.profile')" class="profile" />
                    </li>

                    @if ($viewEmployeeProjects == 'all' && in_array('projects', user_modules()))
                        <li>
                            <x-tab :href="route('employees.show', $employee->id) . '?tab=projects'" :text="__('app.menu.projects')" ajax="false" class="projects" />
                        </li>
                    @endif

                    @if ($viewEmployeeTasks == 'all' && in_array('tasks', user_modules()))
                        <li>
                            <x-tab :href="route('employees.show', $employee->id) . '?tab=tasks'" :text="__('app.menu.tasks')" ajax="false" class="tasks" />
                        </li>
                    @endif

                    @if ($viewEmployeeAttendance != 'none' && $viewEmployeeAttendance != 5 && in_array('attendance', user_modules()))
                        <li>
                            <x-tab :href="route('employees.show', $employee->id) . '?tab=attendance'" :text="__('app.menu.attendance')" ajax="false" class="attendance" />
                        </li>
                    @endif

                    @if (in_array('leaves', user_modules()) && ($viewLeavePermission == 'all' || ($viewLeavePermission == 'owned' || $viewLeavePermission == 'both') && $employee->id == user()->id ))
                        <li>
                            <x-tab :href="route('employees.show', $employee->id) . '?tab=leaves'" :text="__('app.menu.leaves')" ajax="false" class="leaves" />
                        </li>

                    <li>
                        <x-tab :href="route('employees.show', $employee->id) . '?tab=leaves-quota'" :text="__('app.menu.leavesQuota')" class="leaves-quota" />
                    </li>
                    @endif

                    @if ($viewEmployeeTimelogs == 'all' && in_array('timelogs', user_modules()))
                        <li>
                            <x-tab :href="route('employees.show', $employee->id) . '?tab=timelogs'" :text="__('app.menu.timeLogs')" ajax="false" class="timelogs" />
                        </li>
                    @endif

                    @if ($viewDocumentPermission != 'none')
                        <li>
                            <x-tab :href="route('employees.show', $employee->id) . '?tab=documents'" :text="__('app.menu.documents')" class="documents" />
                        </li>
                    @endif

                    @if ($showFullProfile && ($manageEmergencyContact == 'all' || $employee->id == user()->id))
                        <li>
                            <x-tab :href="route('employees.show', $employee->id) . '?tab=emergency-contacts'" :text="__('modules.emergencyContact.emergencyContact')" class="emergency-contacts" />
                        </li>
                    @endif

                    @if ($viewIncrementPermission != 'none')
                        <li>
                            <x-tab :href="route('employees.show', $employee->id) . '?tab=increment-promotions'" :text="__('modules.incrementPromotion.incrementPromotions')" class="increment-promotions" />
                        </li>
                    @endif

                    @if ($viewTickets == 'all' && in_array('tickets', user_modules()))
                        <li>
                            <x-tab :href="route('employees.show', $employee->id) . '?tab=tickets'" :text="__('modules.tickets.ticket')" ajax="false" class="tickets" />
                        </li>
                    @endif

                    @if ($showFullProfile && !in_array('client', user_roles()))
                        <li>
                            <x-tab :href="route('employees.show', $employee->id) . '?tab=appreciation'" :text="__('app.menu.appreciation')" class="appreciation" />
                        </li>
                    @endif

                    @if ($manageShiftPermission == 'all' && in_array('attendance', user_modules()))
                        <li>
                            <x-tab :href="route('employees.show', $employee->id) . '?tab=shifts'" :text="__('app.menu.shiftRoster')" class="shifts" />
                        </li>
                    @endif

                    @if ($manageRolePermissionSetting == 'all')
                        <li>
                            <x-tab :href="route('employees.show', $employee->id) . '?tab=permissions'" :text="__('modules.permission.permissions')" class="permissions" />
                        </li>
                    @endif

                    @if ($showFullProfile)
                        <li>
                            <x-tab :href="route('employees.show', $employee->id) . '?tab=activity'" :text="__('modules.employees.activity')" class="activity" />
                        </li>
                    @endif

                    @if($viewImmigrationPermission == 'all' ||  (in_array($viewImmigrationPermission, ['added', 'owned', 'both']) && user()->id == $employee->id))
                        <li>
                            <x-tab :href="route('employees.show', $employee->id) . '?tab=immigration'" :text="__('modules.employees.immigration')" class="immigration" />
                        </li>
                    @endif
                </ul>
            </nav>
        </div>

        <a class="mb-0 d-block d-lg-none text-dark-grey ml-auto mr-2 border-left-grey" onclick="openClientDetailSidebar()"><i class="fa fa-ellipsis-v "></i></a>
    </div>
@endsection

@section('content')
    <div class="content-wrapper pt-0 border-top-0 client-detail-wrapper">
        <form action="" id="filter-form">
            <div class="d-block d-lg-flex d-md-flex my-3">
                <!-- STATUS START -->
                <div class="select-box py-2 px-0 mr-3">
                    <x-forms.label :fieldLabel="__('app.year')" fieldId="year" />
                    <select class="form-control select-picker" name="year" id="year">
                        @for ($i = $year; $i >= $year - 4; $i--)
                            <option @if ($i == $year) selected @endif value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <!-- STATUS END -->
                <div class="select-box py-2 px-0 mr-3">
                    <x-forms.label :fieldLabel="__('app.month')" fieldId="month" />
                    <select class="form-control select-picker" name="month" id="month" data-live-search="true"
                        data-size="8">
                        <x-forms.months :selectedMonth="$month" fieldRequired="true"/>
                    </select>
                </div>

                <!-- RESET START -->
                {{-- <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0"> --}}
                <div class="select-box py-2 px-0 mr-3" style="margin-top:2rem !important;">
                    <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                        @lang('app.clearFilters')
                    </x-forms.button-secondary>
                </div>
                <!-- RESET END -->
            </div>
        </form>

        <div class="d-flex justify-content-between action-bar">
            <div id="table-actions" class="align-items-center">
                @if ($addAttendancePermission == 'all' || $addAttendancePermission == 'added')
                    <x-forms.link-primary :link="route('attendances.create').'?default_assign='.$employee->id" class="mr-3 openRightModal float-left"
                                       data-redirect-url="{{ url()->full() }}" icon="plus">
                        @lang('modules.attendance.markAttendance')
                    </x-forms.link-primary>
                @endif
            </div>

        </div>

        <x-cards.data class="mt-3">
            <div class="row">
                <div class="col-md-12">
                    <span class="f-w-500 mr-1">@lang('app.note'):</span> <i class="fa fa-star text-warning"></i> <i
                        class="fa fa-arrow-right text-lightest f-11 mx-1"></i> @lang('app.menu.holiday') &nbsp;|&nbsp;<i
                        class="fa fa-calendar-week text-red"></i> <i class="fa fa-arrow-right text-lightest f-11 mx-1"></i>
                    @lang('modules.attendance.dayOff') &nbsp;|&nbsp;
                    <i class="fa fa-check text-success"></i> <i class="fa fa-arrow-right text-lightest f-11 mx-1"></i>
                    @lang('modules.attendance.present') &nbsp;|&nbsp; <i class="fa fa-star-half-alt text-red"></i> <i
                        class="fa fa-arrow-right text-lightest f-11 mx-1"></i>
                    @lang('modules.attendance.halfDay') &nbsp;|&nbsp; <i class="fa fa-exclamation-circle text-warning"></i> <i
                        class="fa fa-arrow-right text-lightest f-11 mx-1"></i>
                    @lang('modules.attendance.late') &nbsp;|&nbsp; <i class="fa fa-times text-lightest"></i> <i
                        class="fa fa-arrow-right text-lightest f-11 mx-1"></i>
                    @lang('modules.attendance.absent') &nbsp;|&nbsp; <i class="fa fa-plane-departure text-danger"></i> <i
                        class="fa fa-arrow-right text-lightest f-11 mx-1"></i>
                    @lang('modules.attendance.leave')
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" id="attendance-data"></div>
            </div>
        </x-cards.data>
    </div>
@endsection

@push('scripts')

    <script>
        $("body").on("click", ".project-menu .ajax-tab", function(event) {
            event.preventDefault();
            $('.project-menu .p-sub-menu').removeClass('active');
            $(this).addClass('active');
            const requestUrl = this.href;
            $.easyAjax({
                url: requestUrl,
                blockUI: true,
                container: ".content-wrapper",
                historyPush: true,
                success: function(response) {
                    if (response.status == "success") {
                        $('.content-wrapper').html(response.html);
                        init('.content-wrapper');
                    }
                }
            });
        });
    </script>

    <script>
        const activeTab = "{{ $activeTab }}";
        $('.project-menu .' + activeTab).addClass('active');
    </script>

    <script>
        /*******************************************************
                 More btn in projects menu Start
        *******************************************************/

        const container = document.querySelector('.tabs');
        const primary = container.querySelector('.-primary');
        const primaryItems = container.querySelectorAll('.-primary > li:not(.-more)');
        container.classList.add('--jsfied'); // insert "more" button and duplicate the list

        primary.insertAdjacentHTML('beforeend', `
        <li class="-more">
            <button type="button" class="px-4 h-100 bg-grey d-none d-lg-flex align-items-center" aria-haspopup="true" aria-expanded="false">
            {{__('app.more')}} <span>&darr;</span>
            </button>
            <ul class="-secondary" id="hide-project-menues">
            ${primary.innerHTML}
            </ul>
        </li>
        `);
        const secondary = container.querySelector('.-secondary');
        const secondaryItems = secondary.querySelectorAll('li');
        const allItems = container.querySelectorAll('li');
        const moreLi = primary.querySelector('.-more');
        const moreBtn = moreLi.querySelector('button');
        moreBtn.addEventListener('click', e => {
            e.preventDefault();
            container.classList.toggle('--show-secondary');
            moreBtn.setAttribute('aria-expanded', container.classList.contains('--show-secondary'));
        }); // adapt tabs

        const doAdapt = () => {
            // reveal all items for the calculation
            allItems.forEach(item => {
                item.classList.remove('--hidden');
            }); // hide items that won't fit in the Primary

            let stopWidth = moreBtn.offsetWidth;
            let hiddenItems = [];
            const primaryWidth = primary.offsetWidth;
            primaryItems.forEach((item, i) => {
                if (primaryWidth >= stopWidth + item.offsetWidth) {
                    stopWidth += item.offsetWidth;
                } else {
                    item.classList.add('--hidden');
                    hiddenItems.push(i);
                }
            }); // toggle the visibility of More button and items in Secondary

            if (!hiddenItems.length) {
                moreLi.classList.add('--hidden');
                container.classList.remove('--show-secondary');
                moreBtn.setAttribute('aria-expanded', false);
            } else {
                secondaryItems.forEach((item, i) => {
                    if (!hiddenItems.includes(i)) {
                        item.classList.add('--hidden');
                    }
                });
            }
        };

        doAdapt(); // adapt immediately on load

        window.addEventListener('resize', doAdapt); // adapt on window resize
        // hide Secondary on the outside click

        document.addEventListener('click', e => {
            let el = e.target;

            while (el) {
                if (el === secondary || el === moreBtn) {
                    return;
                }

                el = el.parentNode;
            }

            container.classList.remove('--show-secondary');
            moreBtn.setAttribute('aria-expanded', false);
        });
        /*******************************************************
                 More btn in projects menu End
        *******************************************************/
    </script>

    <script>

        $('#user_id, #department, #designation, #month, #year').on('change', function () {
            if ($('#user_id').val() != "all") {
                $('#reset-filters').removeClass('d-none');
                showTable();
            } else if ($('#department').val() != "all") {
                $('#reset-filters').removeClass('d-none');
                showTable();
            } else if ($('#designation').val() != "all") {
                $('#reset-filters').removeClass('d-none');
                showTable();
            } else if ($('#month').val() != "all") {
                $('#reset-filters').removeClass('d-none');
                showTable();
            } else if ($('#year').val() != "all") {
                $('#reset-filters').removeClass('d-none');
                showTable();
            } else {
                $('#reset-filters').addClass('d-none');
                showTable();
            }
        });

        $('#reset-filters').click(function () {
            $('#filter-form')[0].reset();
            $('.filter-box .select-picker').selectpicker("refresh");
            $('#filter-form .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
        });

        function showTable(loading = true) {

            var year = $('#year').val();
            var month = $('#month').val();
            var userId = "{{ $employee->id }}";
            var department = "{{ $employee->employeeDetail?->department_id }}";
            var designation = "{{ $employee->employeeDetail?->designation_id }}";

            //refresh counts
            var url = "{{ route('attendances.index') }}";

            var token = "{{ csrf_token() }}";

            $.easyAjax({
                data: {
                    '_token': token,
                    year: year,
                    month: month,
                    department: department,
                    designation: designation,
                    userId: userId
                },
                url: url,
                blockUI: loading,
                container: '.content-wrapper',
                success: function (response) {
                    $('#attendance-data').html(response.data);
                }
            });

        }

        $('#attendance-data').on('click', '.view-attendance', function () {
            var attendanceID = $(this).data('attendance-id');
            var url = "{{ route('attendances.show', ':attendanceID') }}";
            url = url.replace(':attendanceID', attendanceID);

            $(MODAL_XL + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_XL, url);
        });

        $('#attendance-data').on('click', '.edit-attendance', function (event) {
            var attendanceDate = $(this).data('attendance-date');
            var userData = $(this).closest('tr').children('td:first');
            var userID = "{{ $employee->id }}";
            var year = $('#year').val();
            var month = $('#month').val();

            var url = "{{ route('attendances.mark', [':userid', ':day', ':month', ':year']) }}";
            url = url.replace(':userid', userID);
            url = url.replace(':day', attendanceDate);
            url = url.replace(':month', month);
            url = url.replace(':year', year);

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_XL, url);
        });

        function editAttendance(id) {
            var url = "{{ route('attendances.edit', [':id']) }}";
            url = url.replace(':id', id);

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        }

        function addAttendance(userID) {
            var date = $('#date').val();
            const attendanceDate = date.split("-");
            let dayTime = attendanceDate[2];
            dayTime = dayTime.split(' ');
            let day = dayTime[0];
            let month = attendanceDate[1];
            let year = attendanceDate[0];

            var url = "{{ route('attendances.add-user-attendance', [':userid', ':day', ':month', ':year']) }}";
            url = url.replace(':userid', userID);
            url = url.replace(':day', day);
            url = url.replace(':month', month);
            url = url.replace(':year', year);

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        }

        showTable(false);
    </script>

@endpush
