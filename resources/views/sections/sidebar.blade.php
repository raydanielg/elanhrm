<!-- SIDEBAR START -->
<aside class="{{ !user()->dark_theme ? 'sidebar-' . $appTheme->sidebar_theme : '' }}">
    <!-- MOBILE CLOSE SIDEBAR PANEL START -->
    <div class="mobile-close-sidebar-panel w-100 h-100" onclick="closeMobileMenu()" id="mobile_close_panel"></div>
    <!-- MOBILE CLOSE SIDEBAR PANEL END -->
    @php
        $userName = (session()->has('clientContact') && session('clientContact')) ? session('clientContact')->contact_name : user()->name;
    @endphp
    <!-- MAIN SIDEBAR START -->
    <div class="main-sidebar" id="mobile_menu_collapse">
        <!-- SIDEBAR BRAND START -->
        <div class="sidebar-brand-box dropdown cursor-pointer {{ user()->dark_theme ? 'bg-dark' : '' }}">
            <div class="dropdown-toggle sidebar-brand d-flex align-items-center justify-content-between  w-100"
                type="link" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                @if (companyOrGlobalSetting()->sidebar_logo_style !== 'full')
                    <!-- SIDEBAR BRAND NAME START -->
                    <div class="sidebar-brand-name">
                        <h1 class="mb-0 f-16 f-w-500 text-white-shade mt-0" data-placement="bottom" data-toggle="tooltip"
                            data-original-title="{{ $appName }}">{{ $appName }}
                            <i class="icon-arrow-down icons pl-2"></i>
                        </h1>
                        <div class="mb-0 position-relative pro-name">
                            <span class="bg-light-green rounded-circle"></span>
                            <p class="f-13 text-lightest mb-0" data-placement="bottom" data-toggle="tooltip"
                                data-original-title="{{ $userName }}">{{ $userName }}</p>
                        </div>
                    </div>
                    <!-- SIDEBAR BRAND NAME END -->
                    <!-- SIDEBAR BRAND LOGO START -->
                    <div class="sidebar-brand-logo">
                        <img src="{{ companyOrGlobalSetting()->logo_url }}">
                    </div>
                    <!-- SIDEBAR BRAND LOGO END -->
                @else
                    <!-- SIDEBAR BRAND NAME START -->
                    <div class="sidebar-brand-name">
                        <h1 class="mb-0 f-16 f-w-500 text-white-shade mt-0" data-placement="bottom"
                            data-toggle="tooltip" data-original-title="{{ $appName }}">
                            <img src="{{ companyOrGlobalSetting()->logo_url }}">
                        </h1>
                    </div>
                    <!-- SIDEBAR BRAND NAME END -->
                    <!-- SIDEBAR BRAND LOGO START -->
                    <div class="sidebar-brand-logo text-white-shade f-12">
                        <i class="icon-arrow-down icons pl-2"></i>
                    </div>
                    <!-- SIDEBAR BRAND LOGO END -->
                @endif
            </div>
            <!-- DROPDOWN - INFORMATION -->
            <div class="dropdown-menu dropdown-menu-right sidebar-brand-dropdown ml-3"
                aria-labelledby="dropdownMenuLink" tabindex="0">
                <div class="d-flex justify-content-between align-items-center profile-box">
                    <a @if(in_array('client', user_roles())) href="{{ route('profile-settings.index') }}" @elseif (user()->is_superadmin) href="{{ route('superadmin.settings.super-admin-profile.index') }}" @else href="{{ route('employees.show', user()->id) }}" @endif >
                        <div class="profileInfo d-flex align-items-center mr-1 flex-wrap">
                            <div class="profileImg mr-2">
                                <img class="h-100" src="{{ $user->image_url }}"
                                    alt="{{ $userName }}">
                            </div>
                            <div class="ProfileData">
                                <h3 class="f-15 f-w-500 text-dark" data-placement="bottom" data-toggle="tooltip"
                                    data-original-title="{{ $userName }}">{{ $userName }}</h3>
                                <p class="mb-0 f-12 text-dark-grey">{{ user()->employeeDetail->designation->name ?? '' }}</p>
                            </div>
                        </div>
                    </a>

                    {{-- SAAS --}}
                    @if(user()->is_superadmin)
                        <a href="{{ route('superadmin.settings.super-admin-profile.index') }}"
                           data-toggle="tooltip"
                           data-original-title="{{ __('app.menu.profileSettings') }}">
                            <i class="side-icon bi bi-pencil-square"></i>
                        </a>
                    @else
                        <a href="{{ route('profile-settings.index') }}" data-toggle="tooltip"
                           data-original-title="{{ __('app.menu.profileSettings') }}">
                            <i class="side-icon bi bi-pencil-square"></i>
                        </a>
                    @endif
                </div>
                @if (checkCompanyCanAddMoreEmployees(user()->company_id))
                    @if (!in_array('client', user_roles()) && ($sidebarUserPermissions['add_employees'] == 4 || $sidebarUserPermissions['add_employees'] == 1) && in_array('employees', user_modules()))
                        <a class="dropdown-item d-flex justify-content-between align-items-center f-15 text-dark invite-member"
                            href="javascript:;">
                            <span>@lang('app.inviteMember') {{ ($companyName) }}</span>
                            <i class="side-icon bi bi-person-plus"></i>
                        </a>
                    @endif
                @endif

                <a class="dropdown-item d-flex justify-content-between align-items-center f-15 text-dark"
                    href="javascript:;">
                    <label for="dark-theme-toggle">@lang('app.darkTheme')</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="dark-theme-toggle"
                            @if (user()->dark_theme) checked @endif>
                        <label class="custom-control-label f-14" for="dark-theme-toggle"></label>
                    </div>
                </a>
                <a class="dropdown-item d-flex justify-content-between align-items-center f-15 text-dark"
                    href="{{ route('logout') }}" onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">
                    @lang('app.logout')
                    <i class="side-icon bi bi-power"></i>
                </a>

                {{-- SAAS --}}
                @include('super-admin.sections.choose-company')

            </div>
        </div>
        <!-- SIDEBAR BRAND END -->

        <!-- SIDEBAR MENU START -->
        <div class="sidebar-menu {{ user()->dark_theme ? 'bg-dark' : '' }}" id="sideMenuScroll">
            {{-- SAAS --}}
            @if(user()->is_superadmin)
                @include('super-admin.sections.super-admin-menu')
            @else()
                @include('sections.menu')
            @endif
        </div>
        <!-- SIDEBAR MENU END -->
    </div>
    <!-- MAIN SIDEBAR END -->
    <!-- Sidebar Toggler -->
    <div
        class="text-center d-flex justify-content-between align-items-center position-fixed sidebarTogglerBox {{ user()->dark_theme ? 'bg-dark' : '' }}">
        <button class="border-0 d-lg-block d-none text-lightest font-weight-bold" id="sidebarToggle"></button>

        <div class="d-flex align-items-center">
            @if(isWorksuite() || user()->is_superadmin)
            <p class="mb-0 text-dark-grey px-1 py-0 rounded f-10">v{{ \Illuminate\Support\Facades\File::get('version.txt') }}</p>
            @endif
            @if(isWorksuiteSaas())
                @if (in_array('admin', user_roles()) )
                    <p class="mb-0"><a href="{{ route('superadmin.faqs.index') }}" class="text-secondary ml-2 f-15" data-toggle="tooltip" data-original-title="{{__('superadmin.contactSupport')}}"><i class="fa fa-question-circle"></i></a></p>
                @elseif(user()->is_superadmin && !global_setting()->frontend_disable)
                    <p class="mb-0"><a target="_blank" data-toggle="tooltip" data-original-title="{{__('superadmin.VisitFrontWebsite')}}" href="{{ route('front.home') }}" class="text-secondary ml-2 f-15"><i class="fa fa-external-link-alt"></i></a></p>
                 @endif
             @endif

        </div>
    </div>
    <!-- Sidebar Toggler -->
</aside>
<!-- SIDEBAR END -->

<script>
    $(document).ready(function() {

        $('.invite-member').click(function() {
            const url = "{{ route('employees.invite_member') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('#dark-theme-toggle').change(function() {
            const darkTheme = ($(this).is(':checked')) ? '1' : '0'

            $.easyAjax({
                type: 'POST',
                url: "{{ route('profile.dark_theme') }}",
                blockUI: true,
                data: {
                    '_token': '{{ csrf_token() }}',
                    'darkTheme': darkTheme
                },
                success: function(response) {
                    if (response.status === 'success') {
                        window.location.reload();
                    }
                }
            });

        });

    });
</script>
