<div class="text-center mb-70">
    <div class="btn-group" data-toggle="buttons">
        <label class="btn btn-round btn-outline btn-dark w-150 active">
            <input type="radio" onchange="planShow('monthly')" name="pricing" value="monthly" autocomplete="off"
                checked>
            @lang('app.monthly')
        </label>
        <label class="btn btn-round btn-outline btn-dark w-150">
            <input type="radio" onchange="planShow('yearly')" name="pricing" value="yearly" autocomplete="off">
            @lang('app.annually')
        </label>
    </div>
</div>

<section class="pricing-section-2 text-center monthly-packages" id="monthlyPlan">
    <div class="container container-scroll" style="max-width: unset">
{{--        <div class="row @if (count($packages) > 5) flex-nowrap @else justify-content-center @endif">--}}
        <div class="row flex-nowrap justify-content-center">

            <div class="col-md-2 pick-plan">
                <div class="pricing pricing-3">
                    <div class="pricing__head boxed planNameTitle">
                        <h3>@lang('superadmin.frontCms.pickPlan')</h3>
                    </div>

                    <ul>
                        <li>@lang('superadmin.max') @lang('app.menu.employees')</li>
                        <li>@lang('superadmin.fileStorage')</li>

                        @foreach ($packageFeatures as $packageFeature)
                            @if (in_array($packageFeature, $activeModule))
                                <li>
                                    <span>{{ __('modules.module.' . $packageFeature) }}</span>
                                </li>
                            @endif
                        @endforeach

                    </ul>
                </div>
            </div>
            @forelse ($packages->where('monthly_status', 1) as $item)
                <div class="col-md-2">
                    <div class="pricing pricing-3">
                        @if ($item->recommended)
                            <div class="pricing__head bg--primary boxed background-color"> <span
                                    class="label">@lang('app.recommended')</span>
                                <h5>{{ ucwords($item->name) }}</h5> <span
                                    class="h1">{{ $item->formatted_monthly_price }}</span>
                                <p class="type--fine-print">@lang('superadmin.frontCms.perMonth'),
                                    {{ $item->currency->currency_code }}.
                                </p>
                            </div>
                        @else
                            <div class="pricing__head bg--secondary boxed planNameHead">
                                <h5>{{ ucwords($item->name) }}</h5> <span
                                    class="h4">{{ $item->formatted_monthly_price }}</span>
                                <p class="type--fine-print">@lang('superadmin.frontCms.perMonth'),
                                    {{ $item->currency->currency_code }}.
                                </p>
                            </div>
                        @endif
                        <ul>
                            <li>{{ $item->max_employees }} &nbsp;</li>
                            @if ($item->max_storage_size == -1)
                                <li>
                                    @lang('superadmin.unlimited')
                                </li>
                            @else
                                <li>
                                    {{ $item->max_storage_size }}

                                    @if($item->storage_unit == 'mb')
                                        @lang('superadmin.mb')
                                    @else
                                        @lang('superadmin.gb')
                                    @endif
                                </li>
                            @endif
                            @php
                                $packageModules = (array) json_decode($item->module_in_package);
                            @endphp
                            @foreach ($packageFeatures as $packageFeature)
                                @if (in_array($packageFeature, $activeModule))
                                    <li>
                                        @if (in_array($packageFeature, $packageModules))
                                            <i class="fa fa-check-circle module-available"></i>
                                        @endif
                                        &nbsp;
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            @empty
                <div class="col-md-2 ">
                    <div class="pricing pricing-3">
                        <div class="pricing__head bg--secondary boxed planNameHead h-full">
                            <h5>@lang('superadmin.noActivePackage')</h5>
                        </div>
                        <ul>
                            <li>&nbsp;</li>
                            <li>&nbsp;</li>
                            @foreach ($packageFeatures as $packageFeature)
                            @if (in_array($packageFeature, $activeModule))
                                <li>
                                    &nbsp;
                                </li>
                            @endif
                        @endforeach
                        </ul>
                    </div>
                </div>
            @endforelse

        </div>
    </div>
</section>
<section class="pricing-section-2 text-center annual-packages" style="display: none;" id="annualPlan">
    <div class="container container-scroll" style="max-width: unset">
{{--        <div class="row @if (count($packages) > 5) flex-nowrap @else justify-content-center @endif">--}}
        <div class="row flex-nowrap  justify-content-center">

            <div class="col-md-2 pick-plan">
                <div class="pricing pricing-3">
                    <div class="pricing__head boxed planNameTitle">
                        <h3>@lang('superadmin.frontCms.pickPlan')</h3>
                    </div>

                    <ul>
                        <li>@lang('superadmin.max') @lang('app.menu.employees')</li>
                        <li>@lang('superadmin.fileStorage')</li>
                        @foreach ($packageFeatures as $packageFeature)
                            @if (in_array($packageFeature, $activeModule))
                                <li>
                                    <span>{{ __('modules.module.' . $packageFeature) }}</span>
                                </li>
                            @endif
                        @endforeach

                    </ul>
                </div>
            </div>

            @forelse ($packages->where('annual_status', 1) as $item)
                <div class="col-md-2 ">
                    <div class="pricing pricing-3">
                        @if ($item->recommended)
                            <div class="pricing__head bg--primary boxed background-color"> <span
                                    class="label">@lang('app.recommended')</span>
                                <h5>{{ ucwords($item->name) }}</h5> <span
                                    class="h1">{{ $item->formatted_annual_price }}</span>
                                <p class="type--fine-print">@lang('superadmin.frontCms.perYear'),
                                    {{ $item->currency->currency_code }}.
                                </p>
                            </div>
                        @else
                            <div class="pricing__head bg--secondary boxed planNameHead">
                                <h5>{{ ucwords($item->name) }}</h5> <span
                                    class="h4">{{ $item->formatted_annual_price }}</span>
                                <p class="type--fine-print">@lang('superadmin.frontCms.perYear'),
                                    {{ $item->currency->currency_code }}.
                                </p>
                            </div>
                        @endif
                        <ul>
                            <li>{{ $item->max_employees }} &nbsp;</li>
                            @if ($item->max_storage_size == -1)
                                <li>
                                    @lang('superadmin.unlimited')
                                </li>
                            @else
                                <li>
                                    {{ $item->max_storage_size }}

                                    @if($item->storage_unit == 'mb')
                                        @lang('superadmin.mb')
                                    @else
                                        @lang('superadmin.gb')
                                    @endif
                                </li>
                            @endif
                            @php
                                $packageModules = (array) json_decode($item->module_in_package);
                            @endphp
                            @foreach ($packageFeatures as $packageFeature)
                                @if (in_array($packageFeature, $activeModule))
                                    <li>
                                        @if (in_array($packageFeature, $packageModules))
                                            <i class="fa fa-check-circle module-available"></i>
                                        @endif
                                        &nbsp;
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            @empty
                <div class="col-md-2 ">
                    <div class="pricing pricing-3">
                        <div class="pricing__head bg--secondary boxed planNameHead h-full">
                            <h5>@lang('superadmin.noActivePackage')</h5>
                        </div>
                        <ul>
                            <li>&nbsp;</li>
                            <li>&nbsp;</li>
                            @foreach ($packageFeatures as $packageFeature)
                            @if (in_array($packageFeature, $activeModule))
                                <li>
                                    &nbsp;
                                </li>
                            @endif
                        @endforeach
                        </ul>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
