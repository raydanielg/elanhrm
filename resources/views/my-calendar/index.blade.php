@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/full-calendar/main.min.css') }}">
@endpush

@section('filter-section')

    <x-filters.filter-box>
        <!-- TYPES START -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.type')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="type" id="type">
                    <option value="all">@lang('app.all')</option>
                    @foreach ($types as $key => $type)
                        <option value="{{ $key }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <!-- TYPES END -->

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
        <x-cards.data>
            <div id="calendar"></div>
        </x-cards.data>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/full-calendar/main.min.js') }}"></script>
    <script src="{{ asset('vendor/full-calendar/locales-all.min.js') }}"></script>
    <script>

        $('#type').on('change keyup', function() {
            if ($('#type').val() != "all") {
                $('#reset-filters').removeClass('d-none');
                loadData();
            } else {
                $('#reset-filters').addClass('d-none');
                loadData();
            }
        });

        $('#reset-filters').click(function() {
            $('#filter-form')[0].reset();
            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');

            loadData();
        });

        var initialLocaleCode = '{{ user()->locale }}';
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: initialLocaleCode,
            timeZone: '{{ company()->timezone }}',
            firstDay: parseInt("{{ attendance_setting()?->week_start_from }}"),
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            navLinks: true,
            selectable: false,
            selectMirror: true,
            select: function(arg) {
                addEventModal(arg.start, arg.end, arg.allDay);
                calendar.unselect()
            },
            eventClick: function(arg) {
                getEventDetail(arg.event.id,arg.event.extendedProps.event_type);
            },
            editable: false,
            dayMaxEvents: true,
            events: {
                url: "{{ route('my-calendar.index') }}",
                extraParams: function() {
                    var type = $('#type').val();

                    return {
                        type: type,
                    };
                }
            },
            eventDidMount: function(info) {
                    $(info.el).css('background-color', info.event.extendedProps.bg_color);
                    $(info.el).css('color', info.event.extendedProps.color);
                    $(info.el).find('td.fc-list-event-title').prepend('<i class="fa '+info.event.extendedProps.icon+'"></i>&nbsp;&nbsp;');
                    // tooltip for leaves
                    if(info.event.extendedProps.event_type == 'leave'){
                        $(info.el).find('td.fc-list-event-title > a').css('cursor','default'); // list view cursor for leave
                        $(info.el).css('cursor','default')
                        $(info.el).tooltip({
                            title: info.event.extendedProps.name,
                            container: 'body',
                            delay: { "show": 50, "hide": 50 }
                        });
                }
            },
            eventTimeFormat: {
                hour: company.time_format == 'H:i' ? '2-digit' : 'numeric',
                minute: '2-digit',
                hour12: company.time_format == 'H:i' ? false : true,
                meridiem: company.time_format == 'H:i' ? false : true
            }
        });

        if (calendarEl != null) {
            calendar.render();
        }

        function loadData() {
            calendar.refetchEvents();
            calendar.destroy();
            calendar.render();
        }

        // Task Detail show in sidebar
        var getEventDetail = function(id,type) {
            if(type == 'ticket')
            {
                var url = "{{ route('tickets.show', ':id') }}";
                    url = url.replace(':id', id);
                    window.location = url;
                    return true;
            }

            openTaskDetail();

            switch (type) {
                case 'task':
                    var url = "{{ route('tasks.show', ':id') }}";
                    break;
                case 'event':
                    var url = "{{ route('events.show', ':id') }}";
                    break;
                case 'holiday':
                    var url = "{{ route('holidays.show', ':id') }}";
                    break;
                case 'leave':
                    var url = "{{ route('leaves.show', ':id') }}";
                    break;
                case 'follow_up':
                    var url = "{{ route('deals.show', ':id') }}";
                    break;
                default:
                    return 0;
                    break;
            }

            url = url.replace(':id', id);

            $.easyAjax({
                url: url,
                blockUI: true,
                container: RIGHT_MODAL,
                historyPush: true,
                success: function(response) {
                    if (response.status == "success") {
                        $(RIGHT_MODAL_CONTENT).html(response.html);
                        $(RIGHT_MODAL_TITLE).html(response.title);
                    }
                },
                error: function(request, status, error) {
                    if (request.status == 403) {
                        $(RIGHT_MODAL_CONTENT).html(
                            '<div class="align-content-between d-flex justify-content-center mt-105 f-21">403 | Permission Denied</div>'
                        );
                    } else if (request.status == 404) {
                        $(RIGHT_MODAL_CONTENT).html(
                            '<div class="align-content-between d-flex justify-content-center mt-105 f-21">404 | Not Found</div>'
                        );
                    } else if (request.status == 500) {
                        $(RIGHT_MODAL_CONTENT).html(
                            '<div class="align-content-between d-flex justify-content-center mt-105 f-21">500 | Something Went Wrong</div>'
                        );
                    }
                }
            });

        };

        // calendar filter
        var hideDropdown = false;

        $('#event-btn').click(function(){
            if(hideDropdown == true)
            {
                $('#cal-drop').hide();
                hideDropdown = false;
            }
            else
            {
                $('#cal-drop').toggle();
                hideDropdown = true;
            }
        });


        $(document).mouseup(e => {

            const $menu = $('.calendar-action');

            if (!$menu.is(e.target) && $menu.has(e.target).length === 0)
            {
                hideDropdown = false;
                $('#cal-drop').hide();
            }
        });
    </script>
@endpush
