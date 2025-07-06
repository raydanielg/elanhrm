@extends('layouts.app')

@section('content')
    <!-- SETTINGS START -->
    <div class="w-100 d-flex">

        <x-super-admin.front-setting-sidebar :activeMenu="$activeSettingMenu" />
        @include('super-admin.common.language-selector-with-view', [ 'route' => 'superadmin.front-settings.features-translation.lang'])
    </div>
    <!-- SETTINGS END -->
@endsection

@push('scripts')

    <script>

        $("body").on("click", "#saveFrontSetting", function(event) {
            updateLang("{{ route('superadmin.front-settings.features-translation.update_lang') }}",true)
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
