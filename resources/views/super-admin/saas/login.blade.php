<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">


    <title>@lang('app.login') | {{ $setting->global_app_name}}</title>
    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $global->favicon_url }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ $global->favicon_url }}">
    <meta name="theme-color" content="#ffffff">

    <!-- Bootstrap CSS -->
    <link type="text/css" rel="stylesheet" media="all"
          href="{{ asset('saas/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/vendor/animate-css/animate.min.css') }}">
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/vendor/slick/slick.css') }}">
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/vendor/slick/slick-theme.css') }}">
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/fonts/flaticon/flaticon.css') }}">
    <link href="{{ asset('front/plugin/froiden-helper/helper.css') }}" rel="stylesheet">
    <!-- Template CSS -->
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/css/main.css') }}">
    <!-- Template Font Family  -->
    <link
        href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&family=Rubik:wght@400;500&display=swap"
        rel="stylesheet">
    <link type="text/css" rel="stylesheet" media="all"
          href="{{ asset('saas/vendor/material-design-iconic-font/css/material-design-iconic-font.min.css') }}">

    @if($global->google_recaptcha_status  && $global->google_captcha_version=="v3")
        <script
            src="https://www.google.com/recaptcha/api.js?render={{ $global->google_recaptcha_v3_site_key }}"></script>

        <script>
            setInterval(function () {

                grecaptcha.ready(function () {
                    grecaptcha.execute('{{ $global->google_recaptcha_v3_site_key }}', {action: 'submit'}).then(function (token) {
                        document.getElementById("recaptcha_token").value = token;
                    });
                });

            }, 3000);

        </script>
    @endif

    <style>
        {!! $setting->auth_css_theme_two !!}

        :root {
            --main-color: {{ $frontDetail->primary_color }};
        }

        .help-block {
            color: #8a1f11 !important;
        }

        @media (max-width: 767px) {
            .login-box form {
                padding: 10px;
            }

            .input-group-text {
                font-size: 13px;
            }

            .login-box h5 {
                padding: 35px 15px 15px;
                font-size: 21px;
                text-align: center;
                font-weight: 600;
            }
        }

        .spinner-border {
            margin-bottom: 4px;
        }

    </style>
    @if($socialAuthSettings->social_auth_enable)
        <style>
            .login-box {
                max-width: 900px
            }
        </style>
    @endif

    @foreach ($frontWidgets as $item)
        @if(!is_null($item->header_script))
            {!! $item->header_script !!}
        @endif

    @endforeach
</head>

<body id="home">


<!-- Topbar -->
@include('super-admin.sections.saas.saas_header')
<!-- END Topbar -->

<!-- Header -->
<!-- END Header -->


<section class="sp-100 login-section" id="section-contact">
    <div class="container">
        <div class="login-box mt-5 shadow bg-white form-section row align-items-center">

            <div class="@if($socialAuthSettings->social_auth_enable) col-lg-7 @else col-lg-12 @endif" id="form-box">
                <h4 class="mb-0">
                    @lang('app.login')
                </h4>

                <form class="form-horizontal form-material" id="save-form" action="{{ route('login') }}" method="POST">
                    {{ csrf_field() }}


                    @if (session('message'))
                        <div class="alert alert-danger m-t-10">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                        <div class="col-xs-12">
                            <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}"
                                   autofocus required="" placeholder="@lang('app.email')">
                            @if ($errors->has('email'))
                                <div class="help-block with-errors">{{ $errors->first('email') }}</div>
                            @endif

                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control" id="password" type="password" name="password" required=""
                                   placeholder="@lang('modules.client.password')">
                            @if ($errors->has('password'))
                                <div class="help-block with-errors">{{ $errors->first('password') }}</div>
                            @endif
                        </div>
                    </div>
                    @if ($global->google_recaptcha_status == 'active' && $global->google_recaptcha_v2_status == 'active')
                        <div class="form-group" id="captcha_container"></div>
                    @endif
                    @if ($errors->has('g-recaptcha-response'))
                        <div class="help-block with-errors">{{ $errors->first('g-recaptcha-response') }}
                        </div>
                    @endif

                    <input type="hidden" id="g_recaptcha" name="g_recaptcha">

                    <div class="form-group">
                        <div class="col-xs-12">
                            <div class="checkbox checkbox-primary float-left p-t-0">
                                <input id="checkbox-signup" type="checkbox"
                                       name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label for="checkbox-signup" class="text-dark"> @lang('app.rememberMe') </label>
                            </div>
                            <a href="{{ route('password.request') }}" class="text-dark float-right"><i
                                    class="fa fa-lock m-r-5"></i> @lang('app.forgotPassword')</a></div>
                    </div>
                    <div class="form-group text-center m-t-10">
                        <div class="col-xs-12">
                            <button
                                class="btn btn-custom btn-block btn-rounded text-uppercase waves-effect waves-light "
                                id="submit-login"
                                type="submit">@lang('app.login')</button>
                        </div>
                    </div>
                    @if($setting->enable_register)
                        <div class="form-group m-b-0">
                            <div class="col-sm-12 text-center">
                                <p>@lang('superadmin.dontHaveAccount')<br>
                                    <a href="{{ route('front.signup.index') }}"
                                       class="text-primary m-l-5 font-bold"><b>@lang('app.signUp')</b></a>
                                </p>
                            </div>
                            @endif
                        </div>
                </form>
            </div>

            @if($socialAuthSettings->social_auth_enable)
                <div class="col-lg-5">
                    <script>
                        const facebook = "{{ route('social_login', 'facebook') }}";
                        const google = "{{ route('social_login', 'google') }}";
                        const twitter = "{{ route('social_login', 'twitter') }}";
                        const linkedin = "{{ route('social_login', 'linkedin') }}";
                    </script>

                    <div class="order-lg-2 ">
                        <div class="row align-items-center">
                            <div class="col-xs-12 col-sm-12  text-center mb-2">
                                @if($socialAuthSettings->facebook_status == 'enable')
                                    <a href="javascript:;" class="btn btn-primary btn-facebook" data-toggle="tooltip"
                                       title="@lang('auth.signInFacebook')" onclick="window.location.href = facebook;"
                                       data-original-title="@lang('auth.signInFacebook')">@lang('auth.signInFacebook')
                                        <i aria-hidden="true" class="zmdi zmdi-facebook"></i> </a>
                                @endif
                            </div>
                            <div class="col-xs-12 col-sm-12 m-t-10 text-center mb-2">
                                @if($socialAuthSettings->google_status == 'enable')

                                    <a href="javascript:;" class="btn btn-primary btn-google" data-toggle="tooltip"
                                       title="@lang('auth.signInGoogle')" onclick="window.location.href = google;"
                                       data-original-title="@lang('auth.signInGoogle')">@lang('auth.signInGoogle') <i
                                            aria-hidden="true" class="zmdi zmdi-google"></i> </a>
                                @endif
                            </div>
                            <div class="col-xs-12 col-sm-12  m-t-10 text-center mb-2">
                                @if($socialAuthSettings->twitter_status == 'enable')
                                    <a href="javascript:;" class="btn btn-primary btn-twitter" data-toggle="tooltip"
                                       title="@lang('auth.signInTwitter')" onclick="window.location.href = twitter;"
                                       data-original-title="@lang('auth.signInTwitter')">@lang('auth.signInTwitter') <i
                                            aria-hidden="true" class="zmdi zmdi-twitter"></i> </a>
                                @endif
                            </div>
                            <div class="col-xs-12 col-sm-12 m--10 text-center mb-lg-4">
                                @if($socialAuthSettings->linkedin_status == 'enable')
                                    <a href="javascript:;" class="btn btn-primary btn-linkedin" data-toggle="tooltip"
                                       title="@lang('auth.signInLinkedin')" onclick="window.location.href = linkedin;"
                                       data-original-title="@lang('auth.signInLinkedin')">@lang('auth.signInLinkedin')
                                        <i aria-hidden="true" class="zmdi zmdi-linkedin"></i> </a>
                                @endif
                            </div>
                        </div>

                    </div>

                </div>
            @endif
        </div>
    </div>
</section>

<!-- END Main container -->

<!-- Cta -->
{{--@include('saas.sections.cta')--}}
<!-- End Cta -->

<!-- Footer -->
@include('super-admin.sections.saas.saas_footer')
<!-- END Footer -->


<!-- Scripts -->
<script src="{{ asset('saas/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('saas/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('saas/vendor/slick/slick.min.js') }}"></script>
<script src="{{ asset('saas/vendor/wowjs/wow.min.js') }}"></script>
<script src="{{ asset('front/plugin/froiden-helper/helper.js') }}"></script>
<script src="{{ asset('saas/js/main.js') }}"></script>
<script src="{{ asset('front/plugin/froiden-helper/helper.js') }}"></script>
<!-- Global Required JS -->
@foreach ($frontWidgets as $item)
    @if(!is_null($item->footer_script))
        {!! $item->footer_script !!}
    @endif

@endforeach
<script>
    $("form#save-form").submit(function () {
        const button = $('form#save-form').find('#submit-login');

        const text = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{__('app.loading')}}';

        button.prop("disabled", true);
        button.html(text);
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

</body>
</html>
