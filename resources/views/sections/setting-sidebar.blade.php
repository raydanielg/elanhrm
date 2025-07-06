{{-- SAAS --}}
@if (user()?->is_superadmin)
    <x-super-admin.setting-sidebar :activeMenu="$activeSettingMenu"/>
@else
    <x-setting-sidebar :activeMenu="$activeSettingMenu"/>
@endif

