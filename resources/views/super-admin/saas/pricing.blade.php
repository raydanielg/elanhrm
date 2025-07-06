@extends('super-admin.layouts.saas-app')
@section('header-section')
    @include('super-admin.saas.section.breadcrumb')
@endsection


@push('head-script')
    @if(count($packages) > 0)
    <style>
        .package-column {
            max-width: 25%;
            flex: 0 0 25%
        }

        .package-contact-btn {
            font-size: 12px;
        }

        .rate p {
            font-size: 12px;
        }

        span.font-weight-bolder {
            font-size: 20px !important;
        }
    </style>
    @endif
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/css/quill.snow.css') }}">
@endpush

@section('content')
    <!-- START Pricing Section -->
    <section class="pricing-section bg-white sp-100">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sec-title mb-5">
                        <h3>{{ $trFrontDetail->price_title }}</h3>
                        <p>{{ $trFrontDetail->price_description }}</p>
                    </div>
                    {{--@if (isset($packageSetting) && isset($trialPackage) && $packageSetting && !is_null($trialPackage))--}}
                        {{--<h4 class="text-center mb-5">{{$packageSetting->trial_message}}</h4>--}}
                    {{--@endif--}}
                </div>

            </div>
            <div class="row mb-3">
                <div class="col-md-4 col-12"></div>
                <div class="col-md-4 col-12">
                    <select class="custom-select custom-select-sm" id="currency">
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}" @selected($currency->id == global_setting()->currency_id)>
                                {{ $currency->currency_name }} ({{ $currency->currency_symbol }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div id="price-plan">
                @include('super-admin.saas.pricing-plan')
            </div>
        </div>
    </section>
    <!-- END Pricing Section -->

    <!-- START Section FAQ -->
    <section class="bg-white sp-100-70 pt-0">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sec-title mb-60">
                        <h3>{{ $trFrontDetail->faq_title }}</h3>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div id="accordion" class="theme-accordion">
                        @forelse($frontFaqs as $frontFaq)
                            <div class="card border-0 mb-30">
                                <div class="card-header border-bottom-0 p-0" id="acc{{ $frontFaq->id }}">
                                    <h5 class="mb-0">
                                        <button class="position-relative text-decoration-none w-100 text-left collapsed"
                                                data-toggle="collapse" data-target="#collapse{{ $frontFaq->id }}"
                                                aria-controls="collapse{{ $frontFaq->id }}">
                                           {{ $frontFaq->question }}
                                        </button>
                                    </h5>
                                </div>

                                <div id="collapse{{ $frontFaq->id }}" class="collapse" aria-labelledby="acc{{ $frontFaq->id }}" data-parent="#accordion">
                                    <div class="card-body ql-editor">
                                        <p>{!! $frontFaq->answer  !!}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END Section FAQ -->

@endsection
@push('footer-script')
<script>
   @if($monthlyPlan <= 0)
        $('.annual_package').removeClass('inactive').addClass('active');
        $('#yearly').removeClass('inactive').addClass('active');
    @else
        $('#monthly').removeClass('inactive').addClass('active');
    @endif
    // #currency on change request and load price plan on that currency
    $('body').on('change', '#currency', function () {
        let currencyId = $(this).val();
        let url = '{{ route('front.pricing') }}';
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

@endpush
