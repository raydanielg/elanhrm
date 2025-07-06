@extends('layouts.app')
@section('content')
    <!-- SETTINGS START -->
    <div class="w-100 d-flex">

        <x-super-admin.front-setting-sidebar :activeMenu="$activeSettingMenu" />

        <x-setting-card>

            <x-slot name="header">
                <div class="s-b-n-header" id="tabs">
                    <h2 class="f-21 font-weight-normal text-capitalize border-bottom-grey mb-0 p-20">
                        @lang($pageTitle)</h2>
                </div>
            </x-slot>

            <!-- LEAVE SETTING START -->
            <div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-4">
                <div class="row">
                    <div class="col-lg-12">
                        @php
                        $css = '/*Enter your auth css after this line*/';

                        if($global->login_ui == 1 && $global->front_design == 1)
                        {
                            $css =  $global->auth_css_theme_two ?: $css;
                        }
                        else{
                            $css =  $global->auth_css ?: $css;
                        }
                        @endphp

                        <div class="form-group my-3">
                            <x-forms.label fieldId="auth_css" :fieldLabel="__('superadmin.authCss')" ></x-forms.label>
                            <textarea class="form-control f-14 pt-2" rows="8" name="auth_css"
                                id="auth_css">{{ $css }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <!-- LEAVE SETTING END -->

            <x-slot name="action">
                <!-- Buttons Start -->
                <div class="w-100 border-top-grey">
                    <x-setting-form-actions>
                        <x-forms.button-primary id="save-form" class="mr-3" icon="check">@lang('app.update')
                        </x-forms.button-primary>
                    </x-setting-form-actions>
                </div>
                <!-- Buttons End -->
            </x-slot>
        </x-setting-card>

    </div>
    <!-- SETTINGS END -->
@endsection

@push('scripts')
    <script src="{{ asset('vendor/ace/ace.js') }}"></script>
    <script src="{{ asset('vendor/ace/theme-twilight.js') }}"></script>
    <script src="{{ asset('vendor/ace/mode-css.js') }}"></script>
    <script src="{{ asset('vendor/ace/jquery-ace.min.js') }}"></script>
    <script>
        $('#auth_css').ace({ theme: 'twilight', lang: 'css' });

        $('#save-form').click(function() {
            $.easyAjax({
                url: "{{ route('superadmin.front-settings.auth_settings.update') }}",
                container: '#editSettings',
                blockUI: true,
                type: "POST",
                data: $('#editSettings').serialize()
            })
        });
    </script>
@endpush
