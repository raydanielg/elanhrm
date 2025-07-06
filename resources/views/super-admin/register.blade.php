
<x-auth>
    <form id="login-form" action="{{ route('login') }}" class="ajax-form" method="POST">
        {{ csrf_field() }}

        @if (!$global->registration_open)
            <div class="alert alert-info">
                <div class="text-capitalize f-w-500">{!! preg_replace('~^<p>(.*?)</p>$~', '$1', $signUpMessage->message)!!}</div>
            </div>
        @elseif (session('company_approval_pending'))
            <div class="alert alert-success">
                @lang('superadmin.signUpApprovalPending')
            </div>
        @else
            <h3 class="text-capitalize mb-4 f-w-500">@lang('app.signUp')</h3>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-group text-left">
                <label for="company_name">@lang('modules.client.companyName')</label>
                <input type="text" tabindex="1" name="company_name"
                       class="form-control height-50 f-14 light_text"
                       placeholder="@lang('placeholders.company')" id="company_name" autofocus>
            </div>

            @if(module_enabled('Subdomain'))
                <div class="form-group text-left">
                    <label for="sub_domain clearfix">{{ __('subdomain::app.core.subdomain') }}</label>
                    <div class="input-group">
                        <input type="text" tabindex="2" class="form-control height-50 f-15 light_text"
                               placeholder="subdomain"
                               name="sub_domain" id="sub_domain">
                        <div class="input-group-append">
                            <span class="input-group-text"
                                  id="basic-addon2">.{{ getDomain() }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="form-group text-left">
                <label for="name">@lang('modules.profile.yourName') <sup class="f-14 mr-1">*</sup></label>
                <input type="text" tabindex="3" name="name"
                       class="form-control height-50 f-15 light_text"
                       placeholder="@lang('placeholders.name')" id="name">
            </div>

            <div class="form-group text-left">
                <label for="email">@lang('auth.email') <sup class="f-14 mr-1">*</sup></label>
                <input tabindex="4" type="email" name="email"
                       class="form-control height-50 f-15 light_text"
                       placeholder="@lang('placeholders.email')" id="email">
            </div>

            <div class="form-group text-left">
                <label for="password">@lang('app.password') <sup class="f-14 mr-1">*</sup> </label>
                <x-forms.input-group>
                    <input type="password" name="password" id="password"
                           placeholder="@lang('placeholders.password')" tabindex="5"
                           class="form-control height-50 f-15 light_text">
                    <x-slot name="append">
                        <button type="button" tabindex="6" data-toggle="tooltip"
                                data-original-title="@lang('app.viewPassword')"
                                class="btn btn-outline-secondary border-grey height-50 toggle-password">
                            <i
                                class="fa fa-eye"></i></button>
                    </x-slot>
                </x-forms.input-group>
            </div>

            @if ($global->google_recaptcha_status == 'active')
                <div class="form-group text-left">
                    <input type="hidden" name="g-recaptcha-response">
                    <div class="" id="captcha_container"></div>
                    <input type="hidden" id="g_recaptcha" name="g_recaptcha">
                </div>
            @endif


            @if ($errors->has('g-recaptcha-response'))
                <div class="invalid-feedback d-block text-left">{{ $errors->first('g-recaptcha-response') }}
                </div>
            @endif

            @if ($globalSetting->sign_up_terms == 'yes')
                <div class="form-group text-left">
                    <input autocomplete="off" id="read_agreement"
                           name="terms_and_conditions" type="checkbox">
                    <label for="read_agreement">@lang('superadmin.superadmin.acceptTerms') <a
                            href="{{ $globalSetting->terms_link }}" target="_blank"
                            id="terms_link">@lang('superadmin.superadmin.termsAndCondition')</a></label>
                </div>
            @endif

            <button type="button" id="submit-register"
                    class="btn-primary f-w-500 rounded w-100 height-50 f-18">
                @lang('app.signUp') <i class="fa fa-arrow-right pl-1"></i>
            </button>


            <a href="{{ route('login') }}"
               class="btn-secondary f-w-500 rounded w-100 height-50 f-15 mt-3">
                @lang('app.login')
            </a>

        @endif

    </form>

    <x-slot name="scripts">

        @if ($globalSetting->google_recaptcha_status == 'active' && $globalSetting->google_recaptcha_v2_status == 'active')
            <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async
                    defer></script>
            <script>
                var gcv3;
                var onloadCallback = function () {
                    // Renders the HTML element with id 'captcha_container' as a reCAPTCHA widget.
                    // The id of the reCAPTCHA widget is assigned to 'gcv3'.
                    gcv3 = grecaptcha.render('captcha_container', {
                        'sitekey': '{{ $globalSetting->google_recaptcha_v2_site_key }}',
                        'theme': 'light',
                        'callback': function (response) {
                            if (response) {
                                $('#g_recaptcha').val(response);
                            }
                        },
                    });
                };
            </script>
        @endif
        @if ($globalSetting->google_recaptcha_status == 'active' && $globalSetting->google_recaptcha_v3_status == 'active')
            <script
                src="https://www.google.com/recaptcha/api.js?render={{ $globalSetting->google_recaptcha_v3_site_key }}"></script>
            <script>
                grecaptcha.ready(function () {
                    grecaptcha.execute('{{ $globalSetting->google_recaptcha_v3_site_key }}').then(function (token) {
                        // Add your logic to submit to your backend server here.
                        $('#g_recaptcha').val(token);
                    });
                });
            </script>
        @endif


        <script>
            $(document).ready(function () {

                $('#submit-register').click(function () {

                    const url = "{{ route('front.signup.store') }}";

                    $.easyAjax({
                        url: url,
                        container: '.login_box',
                        disableButton: true,
                        buttonSelector: "#submit-register",
                        type: "POST",
                        blockUI: true,
                        data: $('#login-form').serialize(),
                        messagePosition: "inline",
                        success: function (response) {
                            if (response.status == 'success') {
                                $('#form-box').remove();
                            } else if (response.status === 'fail') {
                                @if($global->google_recaptcha_status)
                                grecaptcha.reset();
                                @endif
                            }
                        }
                    });
                    @if($global->google_recaptcha_status)
                    grecaptcha.reset();
                    @endif
                });

            // @if (session('message'))
            // Swal.fire({
            //     icon: 'error',
            //     text: '{{ session('message') }}',
            //     showConfirmButton: true,
            //     customClass: {
            //         confirmButton: 'btn btn-primary',
            //     },
            //     showClass: {
            //         popup: 'swal2-noanimation',
            //         backdrop: 'swal2-noanimation'
            //     },
            // })
            // @endif

            });
        </script>
    </x-slot>

</x-auth>
