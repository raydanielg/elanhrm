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
        <script src="https://www.google.com/recaptcha/api.js?render={{ $global->google_recaptcha_v3_site_key }}"></script>

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
        .spinner-border{
            margin-bottom: 4px;
        }

    </style>
    @if(isset($socialAuthSettings) && (in_array('enabled',[$socialAuthSettings->facebook_status,$socialAuthSettings->google_status,$socialAuthSettings->twitter_status,$socialAuthSettings->linkedin_status])))
        @php
            $socialLogin = true;
        @endphp
    @else
        @php
            $socialLogin = false;
        @endphp
    @endif

    @if($socialLogin)
        <style>
            .login-box {
                max-width: 900px
            }
        </style>
    @endif

</head>

<body id="home">


<!-- Topbar -->
@include('super-admin.sections.saas.saas_header')
<!-- END Topbar -->

<!-- Header -->
<!-- END Header -->


<section class="sp-100 login-section" id="section-contact">
    <div class="container">
        <div class="login-box mt-5 shadow bg-white form-section">
            <h4 class="mb-0">
                @lang('app.login')
            </h4>
            <form class="form-horizontal form-material" id="loginform" action="{{ route('login') }}" method="POST">
                {{ csrf_field() }}


                @if (session('message'))
                    <div class="alert alert-danger m-t-10">
                        {{ session('message') }}
                    </div>
                @endif

                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                    <div class="col-xs-12">
                        <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" autofocus required="" placeholder="@lang('app.email')">
                        @if ($errors->has('email'))
                            <div class="help-block with-errors">{{ $errors->first('email') }}</div>
                        @endif

                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12">
                        <div class="checkbox checkbox-primary pull-left p-t-0">
                            <input id="checkbox-signup" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label for="checkbox-signup" class="text-dark"> @lang('app.rememberMe') </label>
                        </div>
                        <a href="{{ route('password.request') }}"  class="text-dark pull-right"><i class="fa fa-lock m-r-5"></i> @lang('app.forgotPassword')</a> </div>
                </div>
                <div class="form-group text-center m-t-20">
                    <div class="col-xs-12">
                        <button class="btn btn-info btn-lg btn-block btn-rounded text-uppercase waves-effect waves-light" type="submit">@lang('app.login')</button>
                    </div>
                </div>

            </form>
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

<script>
    $('#save-form').click(function () {


        $.easyAjax({
            url: '{{route('front.signup.store')}}',
            container: '.form-section',
            type: "POST",
            data: $('#register').serialize(),
            messagePosition: "inline",
            success: function (response) {
                if(response.status == 'success'){
                    $('#form-box').remove();
                }else if (response.status == 'fail')
                {
                    @if($global->google_recaptcha_status)
                            grecaptcha.reset();
                    @endif

                }
            }
        })
    });
</script>

</body>
</html>
