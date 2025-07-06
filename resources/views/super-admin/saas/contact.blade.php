@extends('super-admin.layouts.saas-app')
@section('header-section')
    @include('super-admin.saas.section.breadcrumb')
@endsection

@section('content')
    <!-- START Contact Section -->
    <section class="contact-section bg-white sp-100-70">
        <div class="container">
            @if(!is_null($frontDetail->contact_html))
                <div class="row">
                    <div class="col-md-10 mx-auto">
                        {!! $frontDetail->contact_html !!}
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-md-10 mx-auto">
                    @if($frontDetail->address || $frontDetail->email || $frontDetail->phone)
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="contact-info row">
                                @if($frontDetail->address)
                                <div class="mobile-device col-md-4 col-lg-6"><span class="fa fa-home fa-fw style"></span><span class="heading-info">@lang('app.address')</span>
                                    <div class="address-content">{{ $frontDetail->address }}</div>
                                </div>
                                @endif
                                @if($frontDetail->email)
                                <div class="mobile-device col-md-4 col-lg-3"><span class="fa fa-envelope fa-fw style"></span><span class="heading-info">@lang('app.email')</span>
                                    <div class="address-content">{{ $frontDetail->email }}</div>
                                </div>
                                @endif
                                @if($frontDetail->phone)
                                    <div class="mobile-device col-md-4 col-lg-3"><span class="fa fa-phone fa-fw style"></span><span class="heading-info">@lang('app.phone')</span>
                                        <div class="address-content">{{ $frontDetail->phone }}</div>
                                    </div>
                                @endif
                            </div>
                            {{--<h2>Get in Touch</h2>--}}
                        </div>
                    </div>
                    @endif
                <form class="" method="POST" id="contactUs">
                    @csrf
                    <div class="row mb-3">
                        <div id="alert" class="col-sm-12"></div>
                    </div>
                    <div class="row" id="contactUsBox">
                        <div class="form-group mb-4 col-lg-6 col-12">
                            <input type="text" name="name" class="form-control" placeholder="@lang('modules.profile.yourName')"
                                   id="name">
                        </div>
                        <div class="form-group mb-4 col-lg-6 col-12">
                            <input type="email" class="form-control" placeholder="@lang('modules.profile.yourEmail')"
                                   name="email" id="email">
                        </div>
                        <div class="form-group mb-4 col-12">
                            <textarea rows="6" name="message" class="form-control"
                                      placeholder="@lang('modules.messages.message')"
                                      id="message"></textarea>
                        </div>

                        @if ($global->google_recaptcha_status == 'active' && $global->google_recaptcha_v2_status == 'active')
                            <div class="form-group col-12" id="captcha_container"></div>
                            <input type="hidden" id="g_recaptcha" name="g_recaptcha">
                        @endif
                        @if ($global->google_recaptcha_status == 'active' && $global->google_recaptcha_v3_status == 'active')
                            <div class="form-group col-12">
                                <input type="hidden" id="g_recaptcha" name="g_recaptcha">
                            </div>
                        @endif

                        <div class="col-12" style="margin-top: 12px;">
                            <button type="button" class="btn btn-lg btn-custom mt-1" id="contact-submit">
                                {{ $frontMenu->contact_submit }}
                            </button>
                        </div>
                    </div>
                </form>
                </div>
            </div>

        </div>
    </section>
    <!-- END Contact Section -->
@endsection
@push('footer-script')
    <script>
        $('#contact-submit').click(function () {

            $.easyAjax({
                url: "{{route('front.contact-us')}}",
                container: '#contactUs',
                blockUI: true,
                type: "POST",
                data: $('#contactUs').serialize(),
                messagePosition: "inline",
                success: function (response) {
                    if (response.status === 'success') {
                        $('#contactUsBox').remove();
                    }
                }
            })
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
