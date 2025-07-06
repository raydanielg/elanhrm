<x-cards.data>

    <div id="monthly-plan">
        <div class="price-wrap border row no-gutters">
            <div class="diff-table col-6 col-md-2">
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

            <div class="all-plans col-6 col-md-10">
                <div class="row no-gutters flex-nowrap flex-wrap overflow-x-auto row-scroll">
                    @foreach ($packages as $key => $item)
                        @if ($item->monthly_status == '1' || $item->default == 'lifetime')
                            <div class="col-md-2 package-column">
                                <div class="pricing-table @if ($item->is_recommended == 1) price-pro @endif ">
                                    <div class="price-top">
                                        <div class="price-head text-center">
                                            <h5 class="mb-0">{{ $item->name }}</h5>
                                        </div>
                                        <div class="rate">
                                            @if ($item->default == 'no' || $item->default == '$item->default')
                                                @if (!$item->is_free)
                                                    <h2 class="mb-2">
                                                        <span
                                                            class="font-weight-bolder">{{ global_currency_format($item->monthly_price, $item->currency_id) }}</span>

                                                    </h2>
                                                    <p class="mb-0">@lang('superadmin.billedMonthly')</p>

                                                @else
                                                    <h2 class="mb-2">

                                                        <span class="font-weight-bolder">@lang('superadmin.packages.free')</span>

                                                    </h2>
                                                    <p class="mb-0">@lang('superadmin.packages.freeForever')</p>
                                                @endif
                                            @elseif ($item->default == 'lifetime')
                                                   <h2 class="mb-2">
                                                        <span
                                                            class="font-weight-bolder">{{ global_currency_format($item->price, $item->currency_id) }}</span>

                                                    </h2>
                                                    <p class="mb-0">@lang('superadmin.packages.lifeTimepackgeInfo')</p>
                                            @else
                                                <h2 class="mb-2">
                                                    <span class="font-weight-bolder">{{ $item->name }}</span>
                                                </h2>
                                                <p class="mb-0">@lang('superadmin.packages.yourDefaultPlan') <i class="fa fa-info-circle"
                                                        data-toggle="tooltip"
                                                        data-original-title="@lang('superadmin.packages.yourDefaultPlanInfo')"></i></p>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="price-content">
                                        <ul class="ui-list">
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
                                                        <i
                                                            class="bi {{ in_array($packageFeature, $packageModules) ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }}"></i>
                                                        &nbsp;
                                                    </li>
                                                @endif
                                            @endforeach

                                            @if (
                                                $item->is_free ||
                                                    $paymentActive ||
                                                    ($item->id == $company->package_id && $company->package_type == 'annual') ||
                                                    $item->default == 'yes')
                                                <li>
                                                    <x-forms.button-primary @class(['purchase-plan'])
                                                        data-package-id="{{ $item->id }}"
                                                        data-default="{{ $item->default }}"
                                                        id="purchase-plan">@lang('superadmin.packages.choosePlan')</x-forms.button-primary>
                                                </li>
                                            @else
                                                <li>
                                                    @lang('superadmin.noPaymentOptionEnable')
                                                </li>
                                            @endif
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <div id="yearly-plan" class="d-none">
        <div class="price-wrap border row no-gutters">
            <div class="diff-table col-6 col-md-2">
                <div class="price-top">
                    <div class="price-top title">
                        <h3>@lang('superadmin.pickUp') <br> @lang('superadmin.yourPlan')</h3>
                        {{-- @lang('modules.frontCms.pickPlan') --}}
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

            <div class="all-plans col-6 col-md-10">
                <div class="row no-gutters flex-nowrap flex-wrap overflow-x-auto row-scroll">
                    @foreach ($packages as $key => $item)
                        @if ($item->annual_status == '1' || $item->default == 'lifetime')
                            <div class="col-md-2 package-column">
                                <div class="pricing-table @if ($item->is_recommended == 1) price-pro @endif">
                                    <div class="price-top">
                                        <div class="price-head text-center">
                                            <h5 class="mb-0">{{ $item->name }}</h5>
                                        </div>
                                        <div class="rate">

                                            @if ($item->default == 'no')
                                                @if (!$item->is_free)
                                                    <h2 class="mb-2">

                                                        <span
                                                            class="font-weight-bolder">{{ global_currency_format($item->annual_price, $item->currency_id) }}</span>

                                                    </h2>
                                                    <p class="mb-0">@lang('superadmin.billedAnnually')</p>
                                                @else
                                                    <h2 class="mb-2">

                                                        <span class="font-weight-bolder">@lang('superadmin.packages.free')</span>

                                                    </h2>
                                                    <p class="mb-0">@lang('superadmin.packages.freeForever')</p>
                                                @endif
                                            @elseif ($item->default == 'lifetime')
                                                <h2 class="mb-2">
                                                    <span class="font-weight-bolder">{{ global_currency_format($item->price, $item->currency_id) }}</span>

                                                    </h2>
                                                    <p class="mb-0">@lang('superadmin.packages.lifeTimepackgeInfo')</p>
                                            @else
                                                <h2 class="mb-2">
                                                    <span class="font-weight-bolder">{{ $item->name }}</span>
                                                </h2>
                                                <p class="mb-0">@lang('superadmin.packages.yourDefaultPlan') <i class="fa fa-info-circle"
                                                        data-toggle="tooltip"
                                                        data-original-title="@lang('superadmin.packages.yourDefaultPlanInfo')"></i></p>
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
                                                        <i
                                                            class="bi {{ in_array($packageFeature, $packageModules) ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }}"></i>
                                                        &nbsp;
                                                    </li>
                                                @endif
                                            @endforeach
                                            @if (
                                                $item->is_free ||
                                                    $paymentActive ||
                                                    ($item->id == $company->package_id && $company->package_type == 'annual') ||
                                                    $item->default == 'yes')
                                                <li>
                                                    <x-forms.button-primary @class(['purchase-plan'])
                                                        data-package-id="{{ $item->id }}"
                                                        data-default="{{ $item->default }}"
                                                        id="purchase-plan">@lang('superadmin.packages.choosePlan')</x-forms.button-primary>
                                                </li>
                                            @else
                                                <li>
                                                    @lang('superadmin.noPaymentOptionEnable')
                                                </li>
                                            @endif
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

        </div>
    </div>

</x-cards.data>
