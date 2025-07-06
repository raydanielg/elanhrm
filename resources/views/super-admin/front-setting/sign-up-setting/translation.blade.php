@extends('layouts.app')

@section('content')
    <!-- SETTINGS START -->
    <div class="w-100 d-flex">

        <x-super-admin.front-setting-sidebar :activeMenu="$activeSettingMenu" />
        <x-slot name="alert">
            <div class="row">
                <div class="col-md-12">
                    <x-alert type="info" icon="info-circle">
                        @lang('superadmin.registerMessage')
                    </x-alert>
                </div>
            </div>
        </x-slot>
        @include('super-admin.common.language-selector-with-view', [ 'route' => 'superadmin.front-settings.signup_setting.lang'])

    </div>
    <!-- SETTINGS END -->
@endsection

@push('scripts')
    <script>


        $("body").on("click", "#saveFrontSetting", function(event) {
            document.getElementById('message_text').value = document.getElementById('message').children[0].innerHTML;
            updateLang("{{ route('superadmin.front-settings.signup_setting.update_lang') }}",true)
        });

    </script>
@endpush
