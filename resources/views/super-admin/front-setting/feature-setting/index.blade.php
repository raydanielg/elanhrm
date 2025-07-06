@extends('layouts.app')
@section('content')
    <!-- SETTINGS START -->
    <div class="w-100 d-flex">

        <x-super-admin.front-setting-sidebar :activeMenu="$activeSettingMenu" />

        <x-setting-card>

            <x-slot name="header">
                <div class="s-b-n-header" id="tabs">
                    <nav class="tabs px-4 border-bottom-grey">
                        <div class="nav" id="nav-tab" role="tablist">

                            <a class="nav-item nav-link f-15 active image" href="{{ route('superadmin.front-settings.features-settings.index') }}?tab=image"
                               role="tab" aria-controls="nav-featureImage"
                               aria-selected="true">@lang('superadmin.menu.featureWithImage')
                            </a>

                            <a class="nav-item nav-link f-15 icon" href="{{ route('superadmin.front-settings.features-settings.index') }}?tab=icon"
                               role="tab" aria-controls="nav-featureIcon"
                               aria-selected="true">@lang('superadmin.menu.featureWithIcon')
                            </a>
                            @if (global_setting()->front_design)
                                <a class="nav-item nav-link f-15 apps" href="{{ route('superadmin.front-settings.features-settings.index') }}?tab=apps"
                                role="tab" aria-controls="nav-featureApp"
                                aria-selected="true">@lang('superadmin.menu.featurePageApps')
                                </a>

                                <a class="nav-item nav-link f-15 settings" href="{{ route('superadmin.front-settings.features-settings.index') }}?tab=settings"
                                role="tab" aria-controls="nav-featureSetting"
                                aria-selected="true">@lang('superadmin.menu.featurePageSetting')
                                </a>
                            @endif
                        </div>
                    </nav>
                </div>
            </x-slot>

            <x-slot name="buttons">
                <div class="row">

                    <div class="col-md-12 mb-2">
                        <x-forms.button-primary icon="plus" class="image-btn mb-2 addFeature d-none actionBtn" data-type="image">
                            @lang('app.addNew') @lang('superadmin.menu.featureWithImage')
                        </x-forms.button-primary>

                        <x-forms.button-primary icon="plus" class="icon-btn mb-2 addFeature d-none actionBtn" data-type="icon">
                            @lang('app.addNew') @lang('superadmin.menu.featureWithIcon')
                        </x-forms.button-primary>

                        <x-forms.button-primary icon="plus" class="apps-btn mb-2 addFeature d-none actionBtn" data-type="apps">
                            @lang('app.addNew') @lang('superadmin.menu.featurePageApps')
                        </x-forms.button-primary>

                        <x-forms.button-primary icon="plus" class="settings-btn mb-2 addFeature d-none actionBtn" data-type="settings">
                            @lang('app.addNew') @lang('superadmin.menu.featurePageSetting')
                        </x-forms.button-primary>
                    </div>

                </div>
            </x-slot>
            <div class="table-responsive" id="table-view">
                @include($view)
            </div>
        </x-setting-card>

    </div>
    <!-- SETTINGS END -->
@endsection

@push('scripts')
<script>

    /* manage menu active class */
    $('.nav-item').removeClass('active');
    const activeTab = "{{ $type }}";
    $('.' + activeTab).addClass('active');

    showBtn(activeTab);

    function showBtn(activeTab) {
        $('.actionBtn').addClass('d-none');
        $('.' + activeTab + '-btn').removeClass('d-none');
    }

     /* open add faq modal */

    $('body').on('click', '.addFeature', function () {
        var settingId = $(this).data('id');
        var featureSettingId = settingId == undefined ? '' : settingId;
        var type = $(this).data('type');
        var url = "{{ route('superadmin.front-settings.features-settings.create') }}?featureSettingId="+featureSettingId + "&type="+type;
        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });

    /* open add footer modal */
    $('body').on('click', '.edit-feature', function () {
        var id = $(this).data('id');
        var type = $(this).data('type');

        var url = "{{ route('superadmin.front-settings.features-settings.edit', ':id')}}?type="+type;
            url = url.replace(':id', id);

        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });

    $('body').on('click', '.delete-table-row', function() {
        var id = $(this).data('id');
        var type = $(this).data('type');
        var settingId = $(this).data('setting-id');
        var featureSettingId = settingId == undefined ? '' : settingId;

        Swal.fire({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.recoverRecord')",
            icon: 'warning',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: "@lang('messages.confirmDelete')",
            cancelButtonText: "@lang('app.cancel')",
            customClass: {
                confirmButton: 'btn btn-primary mr-3',
                cancelButton: 'btn btn-secondary'
            },
            showClass: {
                popup: 'swal2-noanimation',
                backdrop: 'swal2-noanimation'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                var url = "{{ route('superadmin.front-settings.features-settings.destroy', ':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    blockUI: true,
                    data: {
                        'type': type,
                        'featureSettingId':featureSettingId,
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            $('#table-view').html(response.html);
                            // $('.row'+id).fadeOut();
                        }
                    }
                });
            }
        });
    });

</script>
@endpush
