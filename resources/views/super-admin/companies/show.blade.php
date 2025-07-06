@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')
    @php
        $manageBillingPermission = user()->permission('manage_billing');
    @endphp
        <!-- FILTER START -->
    <!-- PROJECT HEADER STARTmplete -->
    <div class="d-flex filter-box project-header bg-white">

        <div class="mobile-close-overlay w-100 h-100" id="close-client-overlay"></div>
        <div class="project-menu d-lg-flex" id="mob-client-detail">

            <a class="d-none close-it" href="javascript:;" id="close-client-detail">
                <i class="fa fa-times"></i>
            </a>

            <x-tab :href="route('superadmin.companies.show', $company->id)"
                   :text="__('superadmin.company') . ' ' . __('app.details')" class="company" ajax="false"
            />

            @if($manageBillingPermission == 'all')
                <x-tab :href="route('superadmin.companies.show', $company->id).'?tab=billing'" ajax="false"
                       :text="__('superadmin.menu.billing')"
                       class="billing"/>
                <x-tab :href="route('superadmin.companies.show', $company->id).'?tab=headers'"
                       ajax="false"
                       :text="__('superadmin.registeredHeaders')"
                       class="headers"/>
            @endif
        </div>

        <a class="mb-0 d-block d-lg-none text-dark-grey ml-auto mr-2 border-left-grey"
           onclick="openClientDetailSidebar()"><i class="fa fa-ellipsis-v "></i></a>

    </div>
    <!-- FILTER END -->
    <!-- PROJECT HEADER END -->

@endsection


@section('content')

    <div class="content-wrapper border-top-0 client-detail-wrapper">
        @include($view)
    </div>

@endsection

@push('scripts')
    <script>
        $("body").on("click", ".ajax-tab", function (event) {
            event.preventDefault();

            $('.project-menu .p-sub-menu').removeClass('active');
            $(this).addClass('active');


            const requestUrl = this.href;

            $.easyAjax({
                url: requestUrl,
                blockUI: true,
                container: ".content-wrapper",
                historyPush: true,
                success: function (response) {
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
@endpush
