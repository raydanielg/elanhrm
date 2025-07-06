@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/css/bootstrap-colorpicker.css') }}"/>
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
            <div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100 px-4 pt-4">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group mb-4">
                            <x-forms.label fieldId="registration_open"
                                           :fieldLabel="__('superadmin.registrationOpen')"></x-forms.label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                       @if ($registrationStatus->registration_open) checked @endif
                                       class="custom-control-input change-module-setting"
                                       id="registration_open" name="registration_open">
                                <label class="custom-control-label cursor-pointer" for="registration_open"></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group mb-4">
                            <x-forms.label fieldId="enable_register"
                                            :popover="__('superadmin.showSignUpPopover')"
                                            :fieldLabel="__('superadmin.accountSettings.enableRegister')"></x-forms.label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                       @if ($registrationStatus->enable_register) checked @endif
                                       class="custom-control-input change-module-setting"
                                       id="enable_register" name="enable_register">
                                <label class="custom-control-label cursor-pointer" for="enable_register"></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group mb-4">
                            <x-forms.label fieldId="sign_in_show" :popover="__('superadmin.showSignInPopover')"
                                        :fieldLabel="__('superadmin.frontCms.singInButtonShow')"></x-forms.label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                    @if ($frontDetail->sign_in_show == 'yes') checked @endif
                                    class="custom-control-input change-module-setting"
                                    id="sign_in_show" name="sign_in_show" value="yes">
                                <label class="custom-control-label cursor-pointer" for="sign_in_show"></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="form-group mb-4">
                            <x-forms.label fieldId="sign_up_terms" :popover="__('superadmin.superadmin.signUpTermsNote')"
                                        :fieldLabel="__('superadmin.superadmin.showSignUpTerms')"></x-forms.label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                    @if ($global->sign_up_terms == 'yes') checked @endif
                                    class="custom-control-input change-module-setting"
                                    id="sign_up_terms" name="sign_up_terms" value="yes">
                                <label class="custom-control-label cursor-pointer" for="sign_up_terms"></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 @if ($global->sign_up_terms == 'no') d-none @endif" id="terms_link_div">
                        <div class="form-group mb-lg-0 mb-md-0 mb-4">
                            <x-forms.label fieldId="terms_link" :fieldLabel="__('superadmin.superadmin.showSignUpTerms')">
                            </x-forms.label>
                            <div class="input-group">
                                <input type="text" name="terms_link"
                                    class="px-6 position-relative text-dark font-weight-normal form-control height-35 rounded p-0 text-left f-15"
                                    placeholder="@lang('placeholders.url')" value="{{ $global->terms_link }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 language-box @if ($registrationStatus->registration_open) d-none @endif">
                        <x-alert type="info" icon="info-circle">
                            @lang('superadmin.registerMessage')
                        </x-alert>
                    </div>
                </div>
            </div>


            <x-slot name="action">
                <!-- LEAVE SETTING END -->
                <div class="row language-box @if ($registrationStatus->registration_open) d-none @endif">
                    <div class="col-lg-12">
                        <div class="s-b-n-header" id="tabs">
                            <nav class="tabs border-bottom-grey">
                                <ul class="nav -primary" id="nav-tab" role="tablist">
                                    @foreach ($languageSettings->sortBy('language_code') as $language)
                                        <li>
                                            <a class="nav-item nav-link f-15 @if ($loop->first) active @endif lang-{{$language->language_code}}"
                                               data-toggle="tab"
                                               href="{{ route('superadmin.front-settings.sign-up-setting.index') }}?lang={{$language->language_code}}"
                                               role="tab"
                                               aria-controls="nav-{{ $language->language_code }}" aria-selected="true">
                                        <span
                                            class='flag-icon flag-icon-{{ ($language->language_code == 'en') ? 'gb' : strtolower($language->flag_code) }} flag-icon-squared'></span>
                                                {{ $language->language_name }}
                                                @if( isset($allLangTranslation) && in_array($language->id, array_column($allLangTranslation,'language_setting_id')))
                                                    <i class='fa fa-circle ml-1 text-light-green'></i>
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </nav>
                        </div>
                        <div class="ntfcn-tab-content-left w-100 p-20" id="language-section">
                            <div class="col-md-12 ntfcn-tab-content-left w-100 pt-0">
                                <input type="hidden" name="language_setting_id" value="{{ $lang->id }}">

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <x-forms.label fieldId="message"
                                                           :fieldLabel="__('app.message').$lang->label">
                                            </x-forms.label>
                                            <div id="message">{!!  $signUpSetting->message ?? '' !!}</div>
                                            <textarea name="message" id="message_text" class="d-none"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
    <script>
        $(document).ready(function () {
            quillImageLoad('#message');
        });

        $('#save-form').click(function () {
            document.getElementById('message_text').value = document.getElementById('message').children[0].innerHTML;
            $.easyAjax({
                url: "{{ route('superadmin.front-settings.sign-up-setting.update', 1) }}",
                container: '#editSettings',
                blockUI: true,
                type: "POST",
                data: $('#editSettings').serialize(),
                success: function (response) {
                    // This will add green-circle icon
                    addBadge(response);
                }
            })
        });

        function addBadge(response) {
            $(`.lang-${response.lang} .fa-circle`).remove()
            if (response.data) {
                $(`.lang-${response.lang}`).append("<i class='fa fa-circle ml-1 text-light-green'></i>")
            }
        }

        $('#registration_open').change(function () {
            //var id = $(this).data('setting-id');
            var signup = $(this).is(':checked') ? 'active' : 'inactive';

            if ($(this).is(':checked')) {
                $('.language-box').addClass('d-none');

            } else {
                $('.language-box').removeClass('d-none');
            }
        });

        $('#sign_up_terms').change(function () {

            if ($(this).is(':checked')) {
                $('#terms_link_div').removeClass('d-none');
            } else {
                $('#terms_link_div').addClass('d-none');
            }
        });
    </script>
    <script>
        /*******************************************************
         More btn in lang menu Start
         *******************************************************/

        const container = document.querySelector('.tabs');
        const primary = container.querySelector('.-primary');
        const primaryItems = container.querySelectorAll('.-primary > li:not(.-more)');
        container.classList.add('--jsfied'); // insert "more" button and duplicate the list

        primary.insertAdjacentHTML('beforeend', `
        <li class="-more bg-grey">
            <button type="button" class="px-4 h-100 d-lg-flex d-md-flex align-items-center justify-content-center" aria-haspopup="true" aria-expanded="false">
                    More <span>&darr;</span>
            </button>
            <ul class="-secondary" id="hide-project-menues">
                ${primary.innerHTML}
            </ul>
        </li>
        `);
        const secondary = container.querySelector('.-secondary');
        const secondaryItems = secondary.querySelectorAll('li');
        const allItems = container.querySelectorAll('li');
        const moreLi = primary.querySelector('.-more');
        const moreBtn = moreLi.querySelector('button');
        moreBtn.addEventListener('click', e => {
            e.preventDefault();
            container.classList.toggle('--show-secondary');
            moreBtn.setAttribute('aria-expanded', container.classList.contains('--show-secondary'));
        }); // adapt tabs

        const doAdapt = () => {
            // reveal all items for the calculation
            allItems.forEach(item => {
                item.classList.remove('--hidden');
            }); // hide items that won't fit in the Primary

            let stopWidth = moreBtn.offsetWidth;
            let hiddenItems = [];
            const primaryWidth = primary.offsetWidth;
            primaryItems.forEach((item, i) => {
                if (primaryWidth >= stopWidth + item.offsetWidth) {
                    stopWidth += item.offsetWidth;
                } else {
                    item.classList.add('--hidden');
                    hiddenItems.push(i);
                }
            }); // toggle the visibility of More button and items in Secondary

            if (!hiddenItems.length) {
                moreLi.classList.add('--hidden');
                container.classList.remove('--show-secondary');
                moreBtn.setAttribute('aria-expanded', false);
            } else {
                secondaryItems.forEach((item, i) => {
                    if (!hiddenItems.includes(i)) {
                        item.classList.add('--hidden');
                    }
                });
            }
        };

        doAdapt(); // adapt immediately on load

        window.addEventListener('resize', doAdapt); // adapt on window resize
        // hide Secondary on the outside click

        document.addEventListener('click', e => {
            let el = e.target;

            while (el) {
                if (el === secondary || el === moreBtn) {
                    return;
                }

                el = el.parentNode;
            }

            container.classList.remove('--show-secondary');
            moreBtn.setAttribute('aria-expanded', false);
        });
        /*******************************************************
         More btn in projects menu End
         *******************************************************/
    </script>
    <script>
        /* manage menu active class */
        $('.nav-item').removeClass('active');
        var activeTab = "lang-{{ $activeTab }}";
        $('.' + activeTab).addClass('active');

        $("body").on("click", "#editSettings .nav a", function (event) {
            event.preventDefault();

            $('.nav-item').removeClass('active');
            $(this).addClass('active');

            const requestUrl = this.href;

            $.easyAjax({
                url: requestUrl,
                blockUI: true,
                container: "#nav-tabContent",
                historyPush: true,
                success: function (response) {
                    if (response.status === "success") {
                        $('#language-section').html(response.html);
                        $('#language_setting_id').val(response.language_setting_id);
                        init('.settings-box');
                        init('#F');
                    }
                }
            });
        });



        function addBadge(response) {
            $(`.lang-${response.lang} .fa-circle`).remove()
            if (response.data) {
                $(`.lang-${response.lang}`).append("<i class='fa fa-circle ml-1 text-light-green'></i>")
            }
        }
    </script>
@endpush
