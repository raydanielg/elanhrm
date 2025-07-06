<!-- HEADER START -->
<header class="main-header clearfix bg-white" id="header">
    @php
        $addSuperadminPermission = user()->permission('add_superadmin');
        $addPackagePermission = user()->permission('add_packages');
        $addCompanyPermission = user()->permission('add_companies');
        $appSettingPermission = user()->permission('manage_superadmin_app_settings');
    @endphp

    <!-- NAVBAR LEFT(MOBILE MENU COLLAPSE) START-->
    <div class="navbar-left float-left d-flex align-items-center">
        <x-app-title class="d-none d-lg-flex" :pageTitle="$pageTitle"></x-app-title>

        <div class="d-block d-lg-none menu-collapse cursor-pointer position-relative" onclick="openMobileMenu()">
            <div class="mc-wrap">
                <div class="mcw-line"></div>
                <div class="mcw-line center"></div>
                <div class="mcw-line"></div>
            </div>
        </div>

        @if ($checkListCompleted < $checkListTotal && App::environment('codecanyon'))
            <div class="ml-3 d-none d-lg-block d-md-block">
                <span class="f-12 mb-1"><a href="{{ route('superadmin.checklist') }}" class="text-lightest ">
                        @lang('modules.accountSettings.setupProgress')</a>
                    <span class="float-right">{{ $checkListCompleted }}/{{ $checkListTotal }}</span>
                </span>
                <div class="progress" style="height: 5px; width: 150px">
                    <div class="progress-bar bg-primary" role="progressbar"
                         style="width: {{ ($checkListCompleted / $checkListTotal) * 100 }}%;" aria-valuenow="25"
                         aria-valuemin="0" aria-valuemax="100">&nbsp;
                    </div>
                </div>
            </div>
        @endif

    </div>

    <!-- NAVBAR LEFT(MOBILE MENU COLLAPSE) END-->
    <!-- NAVBAR RIGHT(SEARCH, ADD, NOTIFICATION, LOGOUT) START-->
    <div class="page-header-right float-right d-flex align-items-center justify-content-end">

        <ul>

            @if($appSettingPermission == 'all')
                <!-- Sticky Note START -->
                <li data-toggle="tooltip" data-placement="top" title="{{__('modules.accountSettings.clearCache')}}"
                    class="d-none d-sm-block cursor-pointer clear-cache">
                    <div class="d-flex align-items-center">
                        <span class="d-block header-icon-box">
                            <i class="fa fa-eraser f-16 text-dark-grey"></i>
                        </span>
                    </div>
                </li>
            @endif
            <!-- Sticky Note START -->
            <li data-toggle="tooltip" data-placement="top" title="{{__('app.menu.stickyNotes')}}"
                class="d-none d-sm-block">
                <div class="d-flex align-items-center">
                    <a href="{{ route('sticky-notes.index') }}" class="d-block header-icon-box openRightModal">
                        <i class="fa fa-sticky-note f-16 text-dark-grey"></i>
                    </a>
                </div>
            </li>
            <!-- Sticky Note END -->
            <!-- ADD START -->
            @if($addSuperadminPermission == 'all' || $addPackagePermission == 'all' || $addCompanyPermission == 'all')
            <li data-toggle="tooltip" data-placement="top" title="{{__('app.createNew')}}">
                <div class="add_box dropdown">
                    <a class="d-block dropdown-toggle header-icon-box" type="link" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-plus-circle f-16 text-dark-grey"></i>
                    </a>
                    <!-- DROPDOWN - INFORMATION -->
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink" tabindex="0">
                        @if($addCompanyPermission == 'all')
                            <a class="dropdown-item f-14 text-dark openRightModal"
                            href="{{ route('superadmin.companies.create') }}">
                                <i class="fa fa-plus f-w-500 mr-2 f-11"></i>
                                @lang('superadmin.addCompany')
                            </a>
                        @endif
                        @if($addPackagePermission == 'all')
                            <a class="dropdown-item f-14 text-dark openRightModal"
                            href="{{ route('superadmin.packages.create') }}">
                                <i class="fa fa-plus f-w-500 mr-2 f-11"></i>
                                @lang('superadmin.addPackage')
                            </a>
                        @endif
                        @if($addSuperadminPermission == 'all')
                        <a class="dropdown-item f-14 text-dark openRightModal"
                           href="{{ route('superadmin.superadmin.create') }}">
                            <i class="fa fa-plus f-w-500 mr-2 f-11"></i>
                            @lang('superadmin.addSuperAdmin')
                        </a>
                        @endif
                    </div>

                </div>
            </li>
            @endif
            <!-- NOTIFICATIONS START -->
            <li title="{{__('app.newNotifications')}}">
                <div class="notification_box dropdown">
                    <a class="d-block dropdown-toggle header-icon-box show-user-notifications" type="link"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-bell f-16 text-dark-grey"></i>
                        @if ($unreadNotificationCount > 0)
                            <span
                                class="badge badge-primary unread-notifications-count active-timer-count position-absolute">{{ $unreadNotificationCount }}</span>
                        @endif
                    </a>
                    <!-- DROPDOWN - INFORMATION -->
                    <div
                        class="dropdown-menu dropdown-menu-right notification-dropdown border-0 shadow-lg py-0 bg-additional-grey"
                        tabindex="0">
                        <div
                            class="d-flex px-3 justify-content-between align-items-center border-bottom-grey py-1 bg-white">
                            <div class="___class_+?50___">
                                <p class="f-14 mb-0 text-dark f-w-500">@lang('app.newNotifications')</p>
                            </div>
                            @if ($unreadNotificationCount > 0)
                                <div class="f-12 ">
                                    <a href="javascript:;"
                                       class="text-dark-grey mark-notification-read">@lang('app.markRead')</a> |
                                    <a href="{{ route('all-notifications') }}"
                                       class="text-dark-grey">@lang('app.showAll')</a>
                                </div>
                            @endif
                        </div>
                        <div id="notification-list">

                        </div>

                        @if($unreadNotificationCount > 6)
                            <div class="d-flex px-3 pb-1 pt-2 justify-content-center bg-additional-grey">
                                <a href="{{ route('all-notifications') }}"
                                   class="text-darkest-grey f-13">@lang('app.showAll')</a>
                            </div>
                        @endif
                    </div>
                </div>
            </li>
            <!-- NOTIFICATIONS END -->
            <!-- LOGOUT START -->
            <li data-toggle="tooltip" data-placement="top" title="{{__('app.logout')}}">
                <div class="logout_box">
                    <a class="d-block header-icon-box" href="javascript:;" onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                        <i class="fa fa-power-off f-16 text-dark-grey"></i>
                    </a>
                </div>
            </li>
            <!-- LOGOUT END -->
        </ul>
    </div>
    <!-- NAVBAR RIGHT(SEARCH, ADD, NOTIFICATION, LOGOUT) START-->
</header>
<!-- HEADER END -->

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<script>
    $(document).ready(function () {

        $('.show-user-notifications').click(function () {
            const openStatus = $(this).attr('aria-expanded');

            if (typeof openStatus == "undefined" || openStatus == "false") {

                const token = '{{ csrf_token() }}';
                $.easyAjax({
                    type: 'POST',
                    url: "{{ route('show_notifications') }}",
                    container: "#notification-list",
                    blockUI: true,
                    data: {
                        '_token': token
                    },
                    success: function (data) {
                        if (data.status === 'success') {
                            $('#notification-list').html(data.html);
                        }
                    }
                });

            }

        });

        $('.mark-notification-read').click(function () {
            const token = '{{ csrf_token() }}';
            $.easyAjax({
                type: 'POST',
                url: "{{ route('mark_notification_read') }}",
                blockUI: true,
                data: {
                    '_token': token
                },
                success: function (data) {
                    if (data.status === 'success') {
                        $('#notification-list').html('');
                        $('.unread-notifications-count').remove();
                        window.location.reload();
                    }
                }
            });
        });

        $('.clear-cache').click(function () {
            $.easyAjax({
                type: 'GET',
                url: "{{ route('superadmin.superadmin.refresh-cache') }}",
                blockUI: true,
                success: function (data) {
                    if (data.status === 'success') {
                        window.location.reload();
                    }
                }
            });
        });

    });
</script>


