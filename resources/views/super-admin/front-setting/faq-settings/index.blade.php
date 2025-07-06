@extends('layouts.app')
@push('styles')
    <style>
        .ql-editor{
            white-space: unset;
        }
    </style>
@endpush
@section('content')
    <!-- SETTINGS START -->
    <div class="w-100 d-flex">

        <x-super-admin.front-setting-sidebar :activeMenu="$activeSettingMenu"/>


        @include('super-admin.common.language-selector-with-view',[ 'route' => 'superadmin.front-settings.faq-settings.index'])

    </div>
    <hr>
    <!-- SETTINGS END -->
@endsection

@push('scripts')

    <script>

        $("body").on("click", "#saveFrontSetting", function () {
            updateLang("{{ route('superadmin.front-settings.faq_setting.update_lang') }}")
        });

    </script>
@endpush
