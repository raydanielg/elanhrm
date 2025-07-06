@extends('super-admin.layouts.front-app')
@section('header-section')
    <style>
        .mb-3, .my-3{
            margin-bottom: 0px !important;
        }
        .section-header small{
            font-size: 18px;
        }
        .container-scroll > .row{
            overflow-x: auto;
            white-space: nowrap;
        }
        .container-scroll > .row > .col-md-2{
            display: inline-block;
            float: none;
        }
        .pricing__head h3, .pricing__head h5{
            white-space: normal;
        }
        .container .gap-y .col-12 .flexbox{
            justify-content: unset;
        }
    </style>
    @include('super-admin.front.section.header')

@endsection
@section('content')
    <!--
        |‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒`‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
        | Features
        |‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
        !-->
    @include('super-admin.front.section.feature')

    <!--
        |‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
        | Pricing
        |‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
        !-->
    @if(!empty($packages))
        @include('super-admin.front.section.pricing')
    @endif

    <!--
        |‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
        | CONTACT
        |‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
        !-->
    @include('super-admin.front.section.contact')
@endsection
@push('footer-script')
    <script>
        var maxHeight = -1;
        $(document).ready(function() {

            var promise1 = new Promise(function(resolve, reject) {

                $('.planNameHead').each(function() {
                    maxHeight = maxHeight > $(this).height() ? maxHeight : $(this).height();
                });
                // console.log('hello1', maxHeight);
                resolve(maxHeight);
            }).then(function(maxHeight) {
                // console.log(maxHeight);
                $('.planNameHead').each(function() {
                    $(this).height(Math.round(maxHeight));
                });
                $('.planNameTitle').each(function() {
                    $(this).height(Math.round(maxHeight-28));
                });

            });
        });
        function planShow(type){
            if(type == 'monthly'){
                $('#monthlyPlan').show();
                $('#annualPlan').hide();
            }
            else{
                $('#monthlyPlan').hide();
                $('#annualPlan').show();
            }
        }

        $('#save-form').click(function () {

            $.easyAjax({
                url: "{{route('front.contact-us')}}",
                container: '#contactUs',
                type: "POST",
                data: $('#contactUs').serialize(),
                messagePosition: "inline",
                success: function (response) {
                    if(response.status == 'success'){
                        $('#contactUsBox').remove();
                    }
                }
            })
        });

        // #currency on change request and load price plan on that currency
        $('body').on('change', '#currency', function () {
            let currencyId = $(this).val();
            let url = '{{ route('front.pricing_plan') }}';
            $.easyAjax({
                url: url,
                type: "GET",
                data: {
                    'currencyId':currencyId
                },
                success: function (response) {
                    $('#price-plan').html(response.view);
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
