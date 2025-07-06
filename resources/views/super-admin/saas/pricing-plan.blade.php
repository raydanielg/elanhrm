<div class="text-center mb-5">
    <div class="nav price-tabs justify-content-center" role="tablist">
        @if ($monthlyPlan > 0)
            <a class="nav-link active" href="#monthly" role="tab" data-toggle="tab">@lang('app.monthly')</a>
        @endif
        @if ($annualPlan > 0)
            <a class="nav-link annual_package @if (!($monthlyPlan > 0)) active @endif" href="#yearly" role="tab" data-toggle="tab">@lang('app.annually')</a>
        @endif
    </div>
</div>
<div class="tab-content wow fadeIn">
    <div role="tabpanel" class="tab-pane @if ($monthlyPlan > 0) active @endif" id="monthly">
        <div class="container">
            <div class="price-wrap row no-gutters border-left">
                <div class="diff-table col-6 col-md-3 border-top border-bottom">
                    <div class="price-top">
                        <div class="price-top title">
                            <h3>@lang('superadmin.pickUp') <br> @lang('superadmin.yourPlan')</h3>
                        </div>
                        <div class="price-content">

                            <ul>
                                <li>
                                    @lang('superadmin.max') @lang('app.active') @lang('app.menu.employees')
                                </li>
                                <li>
                                    @lang('superadmin.fileStorage')
                                </li>
                                @foreach ($packageFeatures as $packageFeature)
                                    @if (in_array($packageFeature, $activeModule))
                                        <li>
                                            {{ __('modules.module.' . $packageFeature) }}
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="all-plans col-6 col-md-9">
                    <div class="row no-gutters flex-nowrap flex-wrap overflow-x-auto row-scroll">
                        @foreach ($packages as $key => $item)
                            @if ($item->monthly_status == '1' || $item->default == 'lifetime')
                                <div class="col-md-3 package-column border-top border-bottom">
                                    <div class="pricing-table price-@if ($item->is_recommended == 1) price-pro @endif ">
                                        <div class="price-top">
                                            <div class="price-head text-center">
                                                <h5 class="mb-0">{{ $item->name }}</h5>
                                            </div>
                                            <div class="rate">
                                                @if (!$item->is_free)
                                                    <h2 class="mb-2">


                                                    @if ($item->package == 'lifetime' )
                                                        <span class="font-weight-bolder">{{ global_currency_format($item->price, $item->currency_id) }}</span>
                                                    @else
                                                    <span
                                                            class="font-weight-bolder">{{ global_currency_format($item->monthly_price, $item->currency_id) }}</span>
                                                    @endif
                                                    </h2>
                                                    @if ($item->default == 'lifetime')
                                                        <p class="mb-0">@lang('superadmin.packages.lifeTimepackgeInfo')</p>
                                                    @else
                                                         <p class="mb-0">@lang('superadmin.billedMonthly')</p>
                                                    @endif

                                                @else
                                                    <h2 class="mb-2">

                                                        <span class="font-weight-bolder">@lang('superadmin.packages.free')</span>

                                                    </h2>
                                                    <p class="mb-0">@lang('superadmin.packages.freeForever')</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="price-content">
                                            <ul>
                                                <li>
                                                    {{ $item->max_employees }}
                                                </li>

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
                                                                <i class="zmdi zmdi-check-circle blue"></i>
                                                            @else
                                                                <i class="zmdi zmdi-close-circle"></i>
                                                            @endif
                                                            &nbsp;
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                        {{-- <div class="price-bottom py-4 px-2"> --}}
                                        {{-- <a href="#" class="btn btn-border shadow-none">buy now</a> --}}
                                        {{-- </div> --}}
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div role="tabpanel" class="tab-pane @if (!($monthlyPlan > 0)) active @endif" id="yearly">
        <div class="container">
            <div class="price-wrap border-left row no-gutters">
                <div class="diff-table col-6 col-md-3 border-top border-bottom">
                    <div class="price-top">
                        <div class="price-top title">
                            <h3>@lang('superadmin.pickUp') <br> @lang('superadmin.yourPlan')</h3>
                        </div>
                        <div class="price-content">

                            <ul>
                                <li>
                                    @lang('superadmin.max') @lang('app.active') @lang('app.menu.employees')
                                </li>
                                <li>
                                    @lang('superadmin.fileStorage')
                                </li>
                                @foreach ($packageFeatures as $packageFeature)
                                    @if (in_array($packageFeature, $activeModule))
                                        <li>
                                            {{ __('modules.module.' . $packageFeature) }}
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="all-plans col-6 col-md-9">
                    <div class="row no-gutters flex-nowrap flex-wrap overflow-x-auto row-scroll">
                        @foreach ($packages as $key => $item)
                            @if ($item->annual_status == '1' || $item->default == 'lifetime')
                                <div class="col-md-3 package-column border-top border-bottom">
                                    <div class="pricing-table @if ($item->is_recommended == 1) price-pro @endif">
                                        <div class="price-top">
                                            <div class="price-head text-center">
                                                <h5 class="mb-0">{{ $item->name }}</h5>
                                            </div>
                                            <div class="rate">
                                                @if (!$item->is_free)
                                                    <h2 class="mb-2">
                                                    @if ($item->package == 'lifetime' )
                                                        <span class="font-weight-bolder">{{ global_currency_format($item->price, $item->currency_id) }}</span>
                                                    @else
                                                        <span class="font-weight-bolder">{{ global_currency_format($item->annual_price, $item->currency_id) }}</span>
                                                    @endif
                                                    </h2>
                                                    @if ($item->default == 'lifetime')
                                                        <p class="mb-0">@lang('superadmin.packages.lifeTimepackgeInfo')</p>
                                                    @else
                                                         <p class="mb-0">@lang('superadmin.billedAnnually')</p>
                                                    @endif
                                                @else
                                                    <h2 class="mb-2">

                                                        <span class="font-weight-bolder">@lang('superadmin.packages.free')</span>

                                                    </h2>
                                                    <p class="mb-0">@lang('superadmin.packages.freeForever')</p>
                                                @endif

                                            </div>
                                        </div>
                                        <div class="price-content">
                                            <ul>
                                                <li>
                                                    {{ $item->max_employees }}
                                                </li>
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
                                                                <i class="zmdi zmdi-check-circle blue"></i>
                                                            @else
                                                                <i class="zmdi zmdi-close-circle"></i>
                                                            @endif
                                                            &nbsp;
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                        {{-- <div class="price-bottom py-4 px-2"> --}}
                                        {{-- <a href="#" class="btn btn-border shadow-none">buy now</a> --}}
                                        {{-- </div> --}}
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
