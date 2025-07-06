@extends('layouts.app')

@section('content')
    <!-- SETTINGS START -->
    <div class="w-100 d-flex">

        <x-super-admin.front-setting-sidebar :activeMenu="$activeSettingMenu" />

        @include('super-admin.common.language-selector-with-view',[ 'route' => 'superadmin.front-settings.client-settings.index'])

    </div>
    <hr>
    <!-- SETTINGS END -->
@endsection

@push('scripts')
    <script>

        $("body").on("click", "#saveFrontSetting", function() {
            $.easyAjax({
                url: "{{ route('superadmin.front-settings.client_setting.update_lang') }}",
                container: '#editSettings',
                type: "POST",
                disableButton: true,
                blockUI: true,
                data: $('#editSettings').serialize(),
                success: function (response) {
                    // This will add green-circle icon
                    addBadge(response);
                }
            })
        });


        $('.cropper').on('dropify.fileReady', function(e) {
            var inputId = $(this).find('input').attr('id');
            var url = "{{ route('cropper', ':element') }}";
            url = url.replace(':element', inputId);
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });


    </script>
@endpush
