<section class="section" id="section-pricing">
    <div class="container">

        <header class="section-header">
            <h2>{{ $trFrontDetail->price_title }}</h2>
            <hr>
            <p class="lead">{{ $trFrontDetail->price_description }}</p>
        </header>

        @if (isset($packageSetting) && isset($trialPackage) && $packageSetting && !is_null($trialPackage))
            <h4 class="text-center mb-5">{{$packageSetting->trial_message}}</h4>
        @endif
        <div class="row mb-20">
            <div class="col-md-4 col-12"></div>
            <div class="col-md-4 col-12">
                <select class="custom-select custom-select-sm w-full" id="currency">
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}" @selected($currency->id == global_setting()->currency_id)>
                                {{ $currency->currency_name }} ({{ $currency->currency_symbol }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div id="price-plan">
            @include('super-admin.front.section.pricing-plan')
        </div>
    </div>
</section>
