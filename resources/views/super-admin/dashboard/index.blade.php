@extends('layouts.app')

@push('styles')
    <script src="{{ asset('vendor/jquery/frappe-charts.min.iife.js') }}"></script>
@endpush

@section('content')

    <!-- CONTENT WRAPPER START -->
    <div class="px-4 py-0 py-lg-4 border-top-0 super-admin-dashboard">
        <div class="row">
            @include('dashboard.update-message-dashboard')
            @includeIf('dashboard.update-message-module-dashboard')
            <x-cron-message :modal="true"></x-cron-message>
        </div>

        @if(user()->permission('view_companies'))
            <div class="row">
                @if($sidebarSuperadminPermissions['view_companies'] != 5 && $sidebarSuperadminPermissions['view_companies'] != 'none')
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <x-cards.widget :title="__('superadmin.dashboard.totalCompany')" :value="$totalCompanies"
                                        icon="building"/>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <x-cards.widget :title="__('superadmin.dashboard.activeCompany')" :value="$activeCompanies"
                                        icon="store"/>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <x-cards.widget :title="__('superadmin.dashboard.licenseExpired')"
                                        :value="$expiredCompanies"
                                        icon="ban"/>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <x-cards.widget :title="__('superadmin.dashboard.inactiveCompany')"
                                        :value="$inactiveCompanies"
                                        icon="store-slash"/>
                    </div>
                @endif
                @if($sidebarSuperadminPermissions['view_packages'] != 5 && $sidebarSuperadminPermissions['view_packages'] != 'none')
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <x-cards.widget :title="__('superadmin.dashboard.totalPackages')"
                                        :value="$totalPackages"
                                        icon="boxes"/>
                    </div>
                @endif
            </div>

            <div class="row">
                @if($sidebarSuperadminPermissions['view_companies'] != 5 && $sidebarSuperadminPermissions['view_companies'] != 'none')
                    <div class="col-sm-12 col-lg-6 mt-4">
                        @include('super-admin.dashboard.recent-registered-companies')
                    </div>
                    <div class="col-sm-12 col-lg-6 mt-4">
                        @include('super-admin.dashboard.top-user-count-companies')
                    </div>
                @endif
                @if($sidebarSuperadminPermissions['manage_billing'] != 5 && $sidebarSuperadminPermissions['manage_billing'] != 'none')
                    <div class="col-sm-12 col-lg-6 mt-4">
                        @include('super-admin.dashboard.recent-subscriptions')
                    </div>
                    <div class="col-sm-12 col-lg-6 mt-4">
                        @include('super-admin.dashboard.recent-license-expired')
                    </div>
                @endif
                @if($sidebarSuperadminPermissions['view_packages'] != 5 && $sidebarSuperadminPermissions['view_packages'] != 'none')
                    <div class="col-sm-12 col-lg-6 mt-4">
                        @include('super-admin.dashboard.package-company-count')
                    </div>
                @endif
                @if($sidebarSuperadminPermissions['view_companies'] != 5 && $sidebarSuperadminPermissions['view_companies'] != 'none')
                    <div class="col-sm-12 col-lg-6 mt-4">
                        @include('super-admin.dashboard.charts')
                    </div>
                @endif
            </div>
        @endif
    </div>
    <!-- CONTENT WRAPPER END -->
@endsection

@push('scripts')

    <script>
        $('#registration_year').change(function () {
            const year = $(this).val();

            let url = `{{ route('superadmin.super_admin_dashboard') }}`;
            const string = `?year=${year}`;
            url += string;

            window.location.href = url;
        });
    </script>

@endpush
