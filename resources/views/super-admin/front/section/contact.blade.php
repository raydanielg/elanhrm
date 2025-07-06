<section class="section bg-img" id="section-contact" style="background-image: url({{ asset('front/img/bg-cup.jpg') }})" data-overlay="8">
    <div class="container">
            <div class="row gap-y">
                <div class="col-12 col-md-10 offset-md-1">
                    {!! $frontDetail->contact_html !!}
                </div>
            </div>
        <div class="row gap-y">

            <div @class([
                "col-12",
                "col-md-6" => ($frontDetail->address || $frontDetail->email || $frontDetail->phone)
                ])>
                <form class="row" method="POST" id="contactUs">
                    @csrf
                    <div class="col-12 col-md-10 offset-md-1 bg-white px-30 py-45 rounded">
                        <p id="alert"></p>
                        <div id="contactUsBox">
                            <div class="form-group">
                                <input class="form-control form-control-lg" type="text" name="name" placeholder="@lang('modules.profile.yourName')">
                            </div>

                            <div class="form-group">
                                <input class="form-control form-control-lg" type="email" name="email" placeholder="@lang('modules.profile.yourEmail')">
                            </div>

                            <div class="form-group">
                                <textarea class="form-control form-control-lg" rows="4" placeholder="@lang('modules.messages.message')" name="message"></textarea>
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


                            <button class="btn btn-lg btn-block btn-primary " type="button" id="save-form">{{$frontMenu->contact_submit}}</button>
                        </div>


                    </div>
                </form>
            </div>
            @if($frontDetail->address || $frontDetail->email || $frontDetail->phone)
                <div class="col-12 col-md-4 offset-md-1 text-inverse pt-40">
                    @if($detail->address)
                    <h6>@lang('app.address')</h6>
                    <p>{{ $detail->address }}</p>
                    <br>
                    @endif

                    @if($detail->phone)
                        <h6>@lang('app.phone')</h6>
                        <p>{{ $detail->phone }}</p>
                        <br>
                    @endif

                    @if($detail->email)
                    <h6>@lang('app.email')</h6>
                    <p>{{ $detail->email }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>
