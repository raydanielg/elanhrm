<div class="col-xl-12 col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-4">
    <div class="row" >
        @include('sections.password-autocomplete-hide')

        <div class="col-lg-12">
            <x-forms.file allowedFileExtensions="png jpg jpeg svg bmp" class="mr-0 mr-lg-2 mr-md-2 cropper"
                :fieldLabel="__('modules.profile.profilePicture')"
                :fieldValue="($user->image ? $user->masked_image_url : $user->image_url)" fieldName="image"
                fieldId="profile-image" :popover="__('modules.themeSettings.logoSize')">
            </x-forms.file>
        </div>

        <div class="col-lg-4">
            <x-forms.text class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.profile.yourName')"
                          fieldRequired="true" :fieldPlaceholder="__('placeholders.name')" fieldName="name"
                          fieldId="name" :fieldValue="$user->name"></x-forms.text>
        </div>


        <div class="col-lg-4">
            <x-forms.text class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.profile.yourEmail')"
                fieldRequired="true" :fieldPlaceholder="__('placeholders.email')" fieldName="email"
                fieldId="email" :fieldValue="$user->email"></x-forms.text>
        </div>

        <div class="col-lg-4">
            <x-forms.label class="mt-3" fieldId="password"
                :fieldLabel="__('modules.profile.yourPassword')">
            </x-forms.label>
            <x-forms.input-group>

                <input type="password" name="password" id="password" autocomplete="off"
                    placeholder="@lang('placeholders.password')" class="form-control height-35 f-14">
                <x-slot name="preappend">
                    <button type="button" data-toggle="tooltip" data-original-title="@lang('app.viewPassword')"
                        class="btn btn-outline-secondary border-grey height-35 toggle-password"><i
                            class="fa fa-eye"></i></button>
                </x-slot>
                <x-slot name="append">
                    <button id="random_password" type="button" data-toggle="tooltip"
                        data-original-title="@lang('modules.client.generateRandomPassword')"
                        class="btn btn-outline-secondary border-grey height-35"><i
                            class="fa fa-random"></i></button>
                </x-slot>
            </x-forms.input-group>
            <small class="form-text text-muted">@lang('modules.client.passwordUpdateNote')</small>
        </div>

        <div class="col-lg-4">
            <div class="form-group my-3">
                <label class="f-14 text-dark-grey mb-12 w-100"
                    for="usr">@lang('modules.emailSettings.emailNotifications')</label>
                <div class="d-flex">
                    <x-forms.radio fieldId="login-yes" :fieldLabel="__('app.enable')"
                        fieldName="email_notifications" fieldValue="1" checked="true"
                        :checked="($user->email_notifications == 1) ? 'checked' : ''">
                    </x-forms.radio>
                    <x-forms.radio fieldId="login-no" :fieldLabel="__('app.disable')" fieldValue="0"
                        fieldName="email_notifications"
                        :checked="($user->email_notifications == 0) ? 'checked' : ''">
                    </x-forms.radio>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <x-forms.select fieldId="country_id" :fieldLabel="__('app.country')" fieldName="country_id"
                            search="true" alignRight="true">
                <option value="">--</option>
                @foreach ($countries as $item)
                    <option data-tokens="{{ $item->iso3 }}" data-phonecode="{{ $item->phonecode }}" data-iso="{{ $item->iso }}"
                            data-content="<span class='flag-icon flag-icon-{{ strtolower($item->iso) }} flag-icon-squared'></span> {{ $item->nicename }}"
                            value="{{ $item->id }}" {{ $item->id == $user->country_id ? 'selected' : '' }}>
                        {{ $item->nicename }}</option>
                @endforeach
            </x-forms.select>
        </div>

        {{-- <div class="col-lg-4">
            <div class="form-group my-3">
                <label class="f-14 text-dark-grey mb-12 w-100" for="usr">@lang('app.rtlTheme')</label>
                <div class="d-flex">
                    <x-forms.radio fieldId="rtl-yes" :fieldLabel="__('app.yes')" fieldName="rtl" fieldValue="1"
                        :checked="($user->rtl == 1) ? 'checked' : ''">
                    </x-forms.radio>
                    <x-forms.radio fieldId="rtl-no" :fieldLabel="__('app.no')" fieldValue="0" fieldName="rtl"
                        :checked="($user->rtl == 0) ? 'checked' : ''">
                    </x-forms.radio>
                </div>
            </div>
        </div> --}}

        <div class="col-lg-4">
            <x-forms.select fieldId="locale" :fieldLabel="__('modules.accountSettings.language')"
                fieldName="locale" search="true">
                @foreach ($languageSettings as $language)
                    <option {{ user()->locale == $language->language_code ? 'selected' : '' }}
                    data-content="<span class='flag-icon flag-icon-{{ ($language->flag_code == 'en') ? 'gb' : strtolower($language->flag_code) }} flag-icon-squared'></span> {{ $language->language_name . ($language->is_rtl == 1 ? ' (' . __('app.rtl') . ')' : '')  }}"
                    value="{{ $language->language_code }}">{{ $language->language_name }}</option>
                @endforeach
            </x-forms.select>
        </div>
        @if (function_exists('sms_setting') && sms_setting()->telegram_status)
            <div class="col-md-4">
                <x-forms.number fieldName="telegram_user_id" fieldId="telegram_user_id"
                    fieldLabel="<i class='fab fa-telegram'></i> {{ __('sms::modules.telegramUserId') }}"
                    :fieldValue="$user->telegram_user_id" :popover="__('sms::modules.userIdInfo')"/>
                <p class="text-bold text-danger">
                    @lang('sms::modules.telegramBotNameInfo')
                </p>
                <p class="text-bold"><span id="telegram-link-text">https://t.me/{{ sms_setting()->telegram_bot_name }}</span>
                    <a href="javascript:;" class="btn-copy btn-secondary f-12 rounded p-1 py-2 ml-1"
                        data-clipboard-target="#telegram-link-text">
                        <i class="fa fa-copy mx-1"></i>@lang('app.copy')</a>
                    <a href="https://t.me/{{ sms_setting()->telegram_bot_name }}" target="_blank" class="btn-secondary f-12 rounded p-1 py-2 ml-1">
                        <i class="fa fa-copy mx-1"></i>@lang('app.openInNewTab')</a>
                </p>
            </div>
        @endif
        @if ($socialAuthSettings->twitter_status == 'enable')
            <div class="col-lg-4">
                <x-forms.text :fieldLabel="__('modules.profile.twitterId')"
                                :fieldPlaceholder="__('placeholders.twitterId')"
                                fieldName="twitter_id" fieldId="twitter_id"
                                :fieldValue="$user->userAuth->twitter_id"/>
            </div>
        @endif
    </div>
</div>

<!-- Buttons Start -->
<div class="w-100 border-top-grey set-btns">
    <x-setting-form-actions>
        <x-forms.button-primary id="save-form" class="mr-3" icon="check">@lang('app.save')
        </x-forms.button-primary>
    </x-setting-form-actions>
</div>
<!-- Buttons End -->
@if (function_exists('sms_setting') && sms_setting()->telegram_status)
    <script src="{{ asset('vendor/jquery/clipboard.min.js') }}"></script>
@endif
<script>

    $(document).ready(function() {


        $('#random_password').click(function() {
            const randPassword = Math.random().toString(36).substr(2, 8);

            $('#password').val(randPassword);
        });

        $('#save-form').on('click', function(e) {
            var url = "{{ route('profile.update', [$user->id]) }}";
            $.easyAjax({
                url: url,
                container: '#editSettings',
                type: "POST",
                disableButton: true,
                buttonSelector: "#save-form",
                file: true,
                data: $('#editSettings').serialize(),
                success : function (response) {
                    if (response.status == 'success') {
                        window.location = response.redirectUrl;
                    }
                }
            });
        });

        $('.cropper').on('dropify.fileReady', function(e) {
            var inputId = $(this).find('input').attr('id');
            var url = "{{ route('cropper', ':element') }}";
            url = url.replace(':element', inputId);
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        @if (function_exists('sms_setting') && sms_setting()->telegram_status)
            var clipboard = new ClipboardJS('.btn-copy');

            clipboard.on('success', function(e) {
                Swal.fire({
                    icon: 'success',
                    text: '@lang("app.urlCopied")',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    customClass: {
                        confirmButton: 'btn btn-primary',
                    },
                    showClass: {
                        popup: 'swal2-noanimation',
                        backdrop: 'swal2-noanimation'
                    },
                })
            });
        @endif
    });

</script>

