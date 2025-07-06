@extends('layouts.app')
@push('styles')
    <style>
        .form_custom_label {
            justify-content: left;
        }

        .ace_gutter {
            z-index: 1 !important;
        }

        .thumbnail.selected p {
            color: white;
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
        }

        .thumbnail p {
            text-align: center;
            margin-top: 10px;
            font-weight: bolder;
        }

        ul.thumbnails.image_picker_selector li .thumbnail.selected {
            @if (!user()->dark_theme)
                background: var(--header_color) !important;
            @else
                background: #000 !important;
            @endif
        }
    </style>
    <link rel="stylesheet" href="{{ asset('vendor/css/bootstrap-colorpicker.css') }}"/>
    <link rel="stylesheet" href="{{ asset('vendor/css/image-picker.min.css') }}">
@endpush
@section('content')
    <!-- SETTINGS START -->
    <div class="w-100 d-flex">

        <x-super-admin.front-setting-sidebar :activeMenu="$activeSettingMenu"/>

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
                        <div class="form-group cursor-pointer">
                            <x-forms.label fieldId="theme"
                                           :fieldLabel="__('superadmin.selectTheme')" fieldRequired="true">
                            </x-forms.label>
                            <select name="theme" class="image-picker image-picker-theme show-labels show-html">
                                <option data-img-src="{{ asset('img/old-design.jpg') }}"
                                        @if ($global->front_design == 0) selected @endif value="0">
                                    @lang('superadmin.theme1')
                                </option>

                                <option data-img-src="{{ asset('img/new-design.jpg') }}" data-toggle="tooltip"
                                        data-original-title="Edit" @if ($global->front_design == 1) selected @endif
                                        value="1">@lang('superadmin.theme2')
                                </option>
                            </select>
                        </div>
                    </div>

                    @if (!module_enabled('Subdomain'))
                        <div class="col-lg-12" id="login_ui_box">
                            <div class="form-group cursor-pointer">
                                <x-forms.label fieldId="login_ui"
                                               :fieldLabel="__('app.login'). ' ' .__('superadmin.theme')"
                                               fieldRequired="true">
                                </x-forms.label>
                                <span class="f-12">(@lang('superadmin.theme2Login')</span>

                                <select name="login_ui" id="login_ui"
                                        class="image-picker show-labels show-html login-theme image-picker-login-theme"
                                        style="color: white">
                                    <option data-img-src="{{ asset('img/old-login.jpg') }}"
                                            @if ($global->login_ui == 0) selected @endif value="0">
                                        @lang('superadmin.theme1')
                                    </option>

                                    <option data-img-src="{{ asset('img/new-login.jpg') }}" data-toggle="tooltip"
                                            data-original-title="Edit" @if ($global->login_ui == 1) selected @endif
                                            value="1">@lang('superadmin.theme2')
                                    </option>

                                </select>
                            </div>
                        </div>
                    @endif

                    <div class="col-lg-6 ">
                        <x-forms.select fieldId="default_language"
                                        :popover="__('superadmin.defaultLanguagePopover')"
                                        :fieldLabel="__('superadmin.frontCms.defaultLanguage')"
                                        fieldName="default_language">

                            @foreach($languageSettings as $language)
                                <option {{ $frontDetail->locale == $language->language_code ? 'selected' : '' }}
                                        data-content="<span class='flag-icon flag-icon-{{ ($language->language_code == 'en') ? 'gb' : strtolower($language->flag_code) }} flag-icon-squared'></span> {{ $language->language_name }}"
                                        value="{{ $language->language_code }}">{{ $language->language_name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group my-3" id="primary_color_div">
                            <x-forms.label fieldId="primary_color"
                                           :popover="__('superadmin.primaryColorTooltip')"
                                           :fieldLabel="__('superadmin.frontCms.primaryColor')">
                            </x-forms.label>
                            <x-forms.input-group class="color-picker">
                                <input type="text" class="form-control height-35 f-14 header_color"
                                       autocomplete="off"
                                       value="{{ $frontDetail->primary_color }}" id="primary_color"
                                       placeholder="{{ __('placeholders.colorPicker') }}" name="primary_color">

                                <x-slot name="append">
                                    <span class="input-group-text height-35 colorpicker-input-addon"><i></i></span>
                                </x-slot>
                            </x-forms.input-group>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-lg-4 pt-5">
                        <x-forms.checkbox :checked="$global->frontend_disable"
                                          :fieldLabel="__('superadmin.superadmin.disableFrontendSite')"
                                          :popover="__('superadmin.frontDisableInfo')"
                                          fieldName="frontend_disable" fieldId="frontend_disable"/>
                    </div>

                    <div class="col-lg-3 @if ($global->frontend_disable) d-none @endif" id="set-homepage-div">
                        <x-forms.select fieldId="setup_homepage" :fieldLabel="__('superadmin.superadmin.setupHomepage')"
                                        fieldName="setup_homepage">
                            <option @if ($global->setup_homepage == 'default') selected @endif value="default">
                                @lang('superadmin.superadmin.defaultLanding')</option>
                            <option @if ($global->setup_homepage == 'signup') selected @endif value="signup">
                                @lang('app.signUp')</option>
                            <option @if ($global->setup_homepage == 'login') selected @endif value="login">
                                @lang('app.login')</option>
                            <option @if ($global->setup_homepage == 'custom') selected @endif value="custom">
                                @lang('superadmin.superadmin.loadCustomUrl')</option>
                        </x-forms.select>
                    </div>
                    <div
                        class="col-lg-5 @if ($global->frontend_disable || ($global->setup_homepage != 'custom')) d-none @endif"
                        id="home_custom_url">
                        <x-forms.text :fieldLabel="__('superadmin.superadmin.customUrl')"
                                      fieldName="custom_homepage_url"
                                      :fieldValue="$global->custom_homepage_url"
                                      fieldId="custom_homepage_url" fieldRequired="true"/>
                    </div>
                </div>
                <div class="row mt-4" id="set-homepage-banner-bg">

                    <div class="col-lg-4 @if ($global->frontend_disable) d-none @endif" >
                        <x-forms.select fieldId="setup_homepage_background" :fieldLabel="__('superadmin.frontCms.setupHomepageBannerBackground')"
                                        fieldName="homepage_background">
                            <option @if ($frontDetail->homepage_background == 'default') selected @endif value="default">
                                @lang('app.default')</option>
                            <option @if ($frontDetail->homepage_background == 'color') selected @endif value="color">
                                @lang('superadmin.frontCms.setBackgroundColor')</option>
                            <option @if ($frontDetail->homepage_background == 'image') selected @endif value="image">
                                @lang('superadmin.frontCms.setBackgroundImage')</option>
                            <option @if ($frontDetail->homepage_background == 'image_and_color') selected @endif value="image_and_color">
                                @lang('superadmin.frontCms.setBackgroundImageColor')</option>
                        </x-forms.select>
                    </div>



                    <div
                        @class([
                            'form-group',
                            'col-lg-4',
                            'set-homepage-banner-bg-fields',
                            'my-3',
                            'd-none' => ($frontDetail->homepage_background == 'default' || $frontDetail->homepage_background == 'image' || $global->frontend_disable)
                        ])
                         id="bg_color_div">
                            <x-forms.label fieldId="background_color"
                                           :fieldLabel="__('superadmin.frontCms.setBackgroundColor')">
                            </x-forms.label>
                            <x-forms.input-group class="color-picker">
                                <input type="text" class="form-control height-35 f-14 header_color"
                                       autocomplete="off"
                                       value="{{ $frontDetail->background_color }}" id="background_color"
                                       placeholder="{{ __('placeholders.colorPicker') }}" name="background_color">

                                <x-slot name="append">
                                    <span class="input-group-text height-35 colorpicker-input-addon"><i></i></span>
                                </x-slot>
                            </x-forms.input-group>
                    </div>

                    <div

                        @class([
                            'form-group',
                            'col-lg-4',
                            'set-homepage-banner-bg-fields',
                            'd-none' => ($frontDetail->homepage_background == 'default' || $frontDetail->homepage_background == 'color' || $global->frontend_disable)
                        ])
                         id="bg_image_div">
                            <x-forms.file allowedFileExtensions="png jpg jpeg svg" class="mr-0 mr-lg-2 mr-md-2 cropper"
                            :fieldLabel="__('superadmin.frontCms.setBackgroundImage')"
                            :fieldValue="$frontDetail->background_image_url" fieldName="background_image"
                            fieldId="background_image"
                            :popover="__('modules.themeSettings.loginBackgroundSize')"/>
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
    <script src="{{ asset('vendor/jquery/bootstrap-colorpicker.js') }}"></script>
    <script src="{{ asset('vendor/jquery/image-picker.min.js') }}"></script>
    <script>
        $('.color-picker').colorpicker();

        // Selecting the main theme
        $(".image-picker-theme").imagepicker({
            show_label: true,
            changed: function (vale, newval) {
                console.log(newval[0]);
                showLoginBlock(newval[0] == 1)
            },
            initialized: function (val) {
                showLoginBlock($(".image-picker-theme").val() == '1')
            }
        });

        function showLoginBlock(condition) {
            if (condition) {
                $('#login_ui_box,#primary_color_div,#set-homepage-div,#set-homepage-banner-bg').show(100);
            } else {
                $('#login_ui_box,#primary_color_div,#set-homepage-div,#set-homepage-banner-bg').hide(100);
            }
        }

        // Selecting login theme
        $(".image-picker-login-theme").imagepicker({
            show_label: true
        });

        $('#frontend_disable').change(function () {
            if ($(this).is(':checked')) {
                $('#set-homepage-div,#home_custom_url, .set-homepage-banner-bg-fields').addClass('d-none');
            } else {
                $('#set-homepage-div, #set-homepage-banner-bg, .set-homepage-banner-bg-fields').removeClass('d-none');

                if ($('#setup_homepage').val() == 'custom') {
                    $('#home_custom_url').removeClass('d-none');
                }
            }
        });

        $('#setup_homepage').change(function () {
            const homepage = $(this).val();

            if (homepage === "custom") {
                $("#home_custom_url").removeClass('d-none');
            } else {
                $("#home_custom_url").addClass('d-none');
            }
        })

        $('#setup_homepage_background').change(function () {
            const homepage = $(this).val();
            $('#bg_image_div, #bg_color_div').addClass('d-none');

            if (homepage === "default") {
                $(".set-homepage-banner-bg-fields,#set-homepage-div").addClass('d-none');
            } else {
                if (homepage == 'image') {
                    $('#bg_image_div').removeClass('d-none');
                } else if (homepage == 'color') {
                    $('#bg_color_div').removeClass('d-none');
                } else {
                    $('#bg_image_div').removeClass('d-none');
                    $('#bg_color_div').removeClass('d-none');
                }
            }
        })

        $('#save-form').click(function () {
            $.easyAjax({
                url: "{{ route('superadmin.front-settings.front_theme_update') }}",
                container: '#editSettings',
                blockUI: true,
                type: "POST",
                file: true,
                disableButton: true,
                buttonSelector: "#save-form",
                data: $('#editSettings').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            })
        });
    </script>
@endpush
