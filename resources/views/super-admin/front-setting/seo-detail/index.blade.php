@extends('layouts.app')

@section('content')

    <!-- SETTINGS START -->
    <div class="w-100 d-flex">

        <x-super-admin.front-setting-sidebar :activeMenu="$activeSettingMenu" />
        @include('super-admin.common.language-selector-with-view', [ 'route' => 'superadmin.front-settings.seo-detail.index'])
    </div>
    <!-- SETTINGS END -->
@endsection

@push('scripts')

    <script>

        $("body").on("click", "#saveFrontSetting", function(event) {
            updateLang("{{ route('superadmin.front-settings.footer-settings.update_lang') }}")
        });

    </script>
    <script>
        /* open add front client modal */
        $('body').on('click', '.edit-seo', function () {
            const id = $(this).data('id');
            let url = "{{ route('superadmin.front-settings.seo-detail.edit', [':id']) }}";
            url = url.replace(':id', id);

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
    </script>
@endpush

