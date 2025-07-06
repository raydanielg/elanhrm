@extends('super-admin.layouts.front-app')

@section('content')
    <section class="section bg-img" id="section-contact"
             style="background-image: url({{ asset('front/img/bg-cup.jpg') }})"
             data-overlay="8">

        <div class="container">
            @if (session('company_approval_pending'))
                <div class="alert alert-success">
                    @lang('superadmin.signUpApprovalPending')
                </div>
            @else
                @if ($registrationStatus->registration_open == 1 && $global->enable_register == true)
                    <div class="row gap-y">

                        <div class="col-12 col-md-8 offset-md-3 form-section">
                            <x-form class="" method="POST" id="register">
                                <div class="col-12 col-md-10 bg-white px-30 py-45 rounded">
                                    <h2 class="text-center m-b-15">@lang('app.signUp')</h2>
                                    <p id="alert"></p>
                                    <div id="form-box">
                                        <div class="row">
                                            <div class="col-12">
                                                @if (module_enabled('Subdomain'))
                                                    <div class="form-group">
                                                        <div class="sub-domain">
                                                            <input type="text" class="form-control"
                                                                   placeholder="your-login-url" name="sub_domain">
                                                            @if (function_exists('getDomain'))
                                                                <span class="domain-text">.{{ getDomain() }}</span>
                                                            @else
                                                                <span
                                                                    class="domain-text">.{{ $_SERVER['SERVER_NAME'] }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="company_name"
                                                           name="company_name"
                                                           placeholder="{{ __('modules.client.companyName') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <input class="form-control form-control-lg" type="text"
                                                           id="name"
                                                           name="name"
                                                           placeholder="{{ __('modules.profile.yourName') }}">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <input class="form-control form-control-lg" type="email"
                                                           id="email"
                                                           name="email"
                                                           placeholder="{{ __('modules.profile.yourEmail') }}">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <input type="password" class="form-control form-control-lg"
                                                           id="password"
                                                           name="password"
                                                           placeholder="{{ __('modules.client.password') }}">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <input type="password" class="form-control form-control-lg"
                                                           id="password_confirmation" name="password_confirmation"
                                                           placeholder="{{ __('app.confirmPassword') }}">
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                @if ($global->google_recaptcha_status == 'active' && $global->google_recaptcha_v2_status == 'active')
                                                    <div class="form-group" id="captcha_container"></div>
                                                    <input type="hidden" id="g_recaptcha" name="g_recaptcha">
                                                @endif
                                                @if ($global->google_recaptcha_status == 'active' && $global->google_recaptcha_v3_status == 'active')
                                                    <div class="form-group">
                                                        <input type="hidden" id="g_recaptcha" name="g_recaptcha">
                                                    </div>
                                                @endif
                                            </div>

                                            @if ($global->sign_up_terms == 'yes')
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <input autocomplete="off" id="read_agreement"
                                                               name="terms_and_conditions" type="checkbox">
                                                        <label
                                                            for="read_agreement">@lang('superadmin.superadmin.acceptTerms')
                                                            <a
                                                                href="{{ $global->terms_link }}"
                                                                target="_blank">@lang('superadmin.superadmin.termsAndCondition')</a></label>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="col-12">
                                                <button class="btn btn-lg btn-block btn-primary" type="button"
                                                        id="save-form">@lang('app.signUp')</button>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </x-form>
                        </div>
                    </div>
                @else
                    <div class="col-12 col-md-10 bg-white px-30 py-45 rounded">
                        <p>{!! preg_replace('~^<p>(.*?)</p>$~', '$1', $signUpMessage->message) !!}</p>
                    </div>
                @endif
            @endif
        </div>
    </section>
@endsection
@push('footer-script')
    <script>
        $('#save-form').click(function () {


            $.easyAjax({
                url: '{{ route('front.signup.store') }}',
                container: '.form-section',
                type: "POST",
                data: $('#register').serialize(),
                messagePosition: "inline",
                success: function (response) {
                    if (response.status == 'success') {
                        $('#form-box').remove();
                    } else if (response.status == 'fail') {
                        @if ($global->google_recaptcha_status)
                        grecaptcha.reset();
                        @endif

                    }
                }
            });
            @if ($global->google_recaptcha_status)
            grecaptcha.reset();
            @endif
        });
    </script>
    @if ($global->google_recaptcha_status == 'active' && $global->google_recaptcha_v2_status == 'active')
        <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async
                defer></script>
        <script>
            var gcv3;
            var onloadCallback = function () {
                // Renders the HTML element with id 'captcha_container' as a reCAPTCHA widget.
                // The id of the reCAPTCHA widget is assigned to 'gcv3'.
                gcv3 = grecaptcha.render('captcha_container', {
                    'sitekey': '{{ $global->google_recaptcha_v2_site_key }}',
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
    @if ($global->google_recaptcha_status == 'active' && $global->google_recaptcha_v3_status == 'active')
        <script
            src="https://www.google.com/recaptcha/api.js?render={{ $global->google_recaptcha_v3_site_key }}"></script>
        <script>
            grecaptcha.ready(function () {
                grecaptcha.execute('{{ $global->google_recaptcha_v3_site_key }}').then(function (token) {
                    // Add your logic to submit to your backend server here.
                    $('#g_recaptcha').val(token);
                });
            });
        </script>
    @endif
@endpush
