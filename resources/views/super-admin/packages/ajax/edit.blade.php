<div class="row">
    <div class="col-sm-12">
        <x-form id="update-package-data-form" method="PUT">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-3 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('superadmin.packages.edit')</h4>
                <div class="row px-3 mb-3">
                    @if($package->default === 'yes')
                        <div class="col-md-12 mt-2">
                            <x-alert type="primary">
                                <span class="mb-12"><strong>Note:</strong></span>
                                <ul>
                                    <li>1. This package cannot be deleted</li>
                                    <li>2. When trial Package gets expired, or customer fails the payment of paid
                                        package, the company goes back to this package
                                    </li>
                                    <li>3. We have limited configuration for this package.</li>
                                </ul>
                            </x-alert>
                        </div>
                    @endif

                    @if($package->default === 'no' || $package->default === 'lifetime')
                        <div class="col-md-12">
                            <x-forms.label fieldId="package_type" :fieldLabel="__('superadmin.packages.choosePackageType')" class="mt-3" />
                        </div>

                        <div class="col-md-12 mb-4">
                            <div class="btn btn btn-light p-2 f-15 border mr-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="package_type" id="package_type_paid" value="paid"  {{ ($package->is_free ? '' : 'checked') }}>
                                    <label class="form-check-label ml-2" for="package_type_paid">
                                        @lang('superadmin.packages.paidPlan')
                                        <i class="fa fa-question-circle" data-toggle="popover" data-placement="top" data-content="@lang('superadmin.packages.paidPlanInfo')" data-html="true" data-trigger="hover"></i>
                                    </label>
                                </div>
                            </div>
                            <div class="btn btn btn-light p-2 f-15 border mr-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="package_type" id="package_type_free" value="free" {{ ($package->is_free ? 'checked' : '') }}>
                                    <label class="form-check-label ml-2" for="package_type_free">
                                        @lang('superadmin.freePlan')
                                        <i class="fa fa-question-circle" data-toggle="popover" data-placement="top" data-content="@lang('superadmin.packages.freePlanInfo')" data-html="true" data-trigger="hover"></i>
                                    </label>
                                </div>
                            </div>

                        </div>
                    @endif

                    <div class="col-lg-4 col-md-6">
                        <x-forms.text :fieldLabel="__('app.name')" fieldName="name" fieldRequired="true" fieldId="name"
                                      :fieldValue="$package->name"/>
                    </div>
                    <div class="col-lg-4 col-md-6 col-xl-3">
                        <x-forms.number :fieldLabel="__('superadmin.max') . ' ' . __('app.menu.employees')"
                                        fieldName="max_employees" fieldRequired="true" fieldId="max_employees"
                                        :fieldValue="$package->max_employees"
                                        :popover="__('superadmin.packages.maxEmployeesInfo')"
                        />
                    </div>
                    <div class="col-lg-4 col-md-6 col-xl-3">
                        <x-forms.number :fieldLabel="__('superadmin.maxStorageSize')" fieldName="max_storage_size"
                                        :fieldValue="$package->max_storage_size" fieldRequired="true"
                                        fieldId="max_storage_size"
                                        :fieldHelp="__('superadmin.packages.maxStorageSizeHelp')"/>
                    </div>
                    <div class="col-lg-4 col-md-6 col-xl-2">
                        <x-forms.select fieldId="storage_unit" :fieldLabel="__('superadmin.storageUnit')"
                                        fieldName="storage_unit">
                            <option value="mb"
                                    @if($package->storage_unit == 'mb') selected @endif>@lang('superadmin.mb')</option>
                            <option value="gb"
                                    @if($package->storage_unit == 'gb') selected @endif>@lang('superadmin.gb')</option>
                        </x-forms.select>
                    </div>
                    @if(!isset($trial))
                    <div class="col-lg-4 col-md-16">
                        <x-forms.select fieldId="sort" :fieldLabel="__('superadmin.position')"
                                fieldName="sort" :popover="__('superadmin.packages.positionInfo')" fieldRequired="true">
                            @for ($i = 1; $i <= $packageCount; $i++)
                                <option value="{{ $i }}" @if ($package->sort == $i) selected @endif>{{ $i }}</option>
                            @endfor
                        </x-forms.select>
                    </div>
                    @endif
                    <div class="col-md-6 col-lg-4 mb-4">
                         <x-forms.select fieldId="package_type" :fieldLabel="__('superadmin.packageType')"
                                        fieldName="package">
                            <option value="standard" @if ($package->package == 'standard') selected @endif>@lang('superadmin.standard')</option>
                            <option value="lifetime" @if ($package->package == 'lifetime') selected @endif>@lang('superadmin.lifetime')</option>
                        </x-forms.select>
                    </div>

                    <div class="col-lg-4 col-md-16 lifetime_price_section @if ($package->is_free) d-none @endif" >
                            <x-forms.number
                                :fieldLabel="__('superadmin.packages.lifeTimePlanprice') . ' (' . $package->currency->currency_symbol . ')'"
                                fieldName="price" fieldRequired="true" :fieldValue="$package->price"
                                fieldId="price"/>
                    </div>

                </div>

                @if($package->default == 'no' || $package->default == 'lifetime')
                    <div class="row px-3 pb-3">
                        <div class="col-md-6 col-lg-6 col-xl-4">
                            <x-forms.checkbox class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.tasks.makePrivate')"
                                            :checked="$package->is_private" fieldName="is_private"
                                            fieldId="is_private" fieldValue="true"
                                            :popover="__('superadmin.packages.privateInfo')"/>
                        </div>
                        <div class="col-md-6 col-lg-6 col-xl-4">
                            <x-forms.checkbox class="mr-0 mr-lg-2 mr-md-2"
                                            :fieldLabel="__('superadmin.packages.isRecommended')"
                                            :checked="$package->is_recommended" fieldName="is_recommended"
                                            fieldId="is_recommended"/>
                        </div>
                    </div>
                @endif

                @if($package->default == 'no' || $package->default == 'lifetime')
                    <h4 class="mt-3 mb-0 p-3 f-21 font-weight-normal text-capitalize border-top-grey payment-title @if ($package->is_free || $package->default == 'lifetime' ) d-none @endif">
                        @lang('superadmin.packages.paymentGatewayPlans')
                    </h4>
                    <div class="row px-3 payment-box @if ($package->is_free || $package->default == 'lifetime' ) d-none @endif">


                        <div class="col-lg-4 col-md-6 mb-4">
                            <x-forms.label fieldId="currency_id" :fieldLabel="__('superadmin.packages.currency')" :popover="__('superadmin.packages.currencyEditInfo')"
                                class="mt-3"  fieldRequired="true"></x-forms.label>
                            <div class="form-group mb-0">
                                <select name="currency_id" class="form-control select-picker" id="currency_id" disabled>
                                    @foreach ($currencies as $currency)
                                    <option value="{{ $currency->id }}" @selected($currency->id == ($package->currency_id ?: $global->currency_id))>
                                        {{ $currency->currency_symbol . ' (' . $currency->currency_code . ')' }}
                                    </option>
                                @endforeach
                                </select>
                                <input type="hidden" name="currency_id" value="{{ $package->currency_id ?: $global->currency_id }}">
                            </div>
                        </div>

                        <div class="col-sm-12"></div>

                         <div class="col-md-6 col-lg-6">
                            <x-forms.checkbox class="mr-0 mr-lg-2 mr-md-2 packages" data-value='monthly'
                                              :fieldLabel="__('superadmin.monthly')" :checked="$package->monthly_status"
                                              fieldName="monthly_status" fieldId="monthly_status" fieldValue="true" />
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <x-forms.checkbox class="mr-0 mr-lg-2 mr-md-2 packages" data-value='annual'
                                              :fieldLabel="__('superadmin.annual')" :checked="$package->annual_status"
                                              fieldName="annual_status" fieldId="annual_status" fieldValue="true"/>
                        </div>

                        <div class="col-md-6">
                            <div class="row monthly_package @if (!$package->monthly_status) d-none @endif">
                                <div class="col-md-12">
                                    <x-forms.number
                                        :fieldLabel="__('superadmin.monthly') . ' ' . __('app.price') . ' (' . $package->currency->currency_symbol . ')'"
                                        fieldName="monthly_price" fieldRequired="true" :fieldValue="$package->monthly_price"
                                        fieldId="monthly_price"/>
                                </div>
                                @if($paymentGateway->stripe_status == 'active')
                                    <div class="col-md-12">
                                        <x-forms.text :fieldLabel="__('superadmin.packages.stripeMonthlyPlanId')"
                                                    :fieldValue="$package->stripe_monthly_plan_id"
                                                    fieldName="stripe_monthly_plan_id" fieldId="stripe_monthly_plan_id"/>
                                    </div>
                                @endif
                                @if($paymentGateway->razorpay_status == 'active')
                                    <div class="col-md-12">
                                        <x-forms.text :fieldLabel="__('superadmin.packages.razorpayMonthlyPlanId')"
                                                    :fieldValue="$package->razorpay_monthly_plan_id"
                                                    fieldName="razorpay_monthly_plan_id"
                                                    fieldId="razorpay_monthly_plan_id"/>
                                    </div>
                                @endif
                                @if($paymentGateway->paystack_status == 'active')
                                    <div class="col-md-12">
                                        <x-forms.text :fieldLabel="__('superadmin.packages.paystackMonthlyPlanId')"
                                                    :fieldValue="$package->paystack_monthly_plan_id"
                                                    fieldName="paystack_monthly_plan_id"
                                                    fieldId="paystack_monthly_plan_id"/>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row annual_package @if (!$package->annual_status) d-none @endif">

                                <div class="col-md-12">
                                    <x-forms.number
                                        :fieldLabel="__('superadmin.annual') . ' ' . __('app.price') . ' (' . $package->currency->currency_symbol . ')'"
                                        fieldName="annual_price" fieldRequired="true" :fieldValue="$package->annual_price"
                                        fieldId="annual_price"/>
                                </div>
                                @if($paymentGateway->stripe_status == 'active')
                                    <div class="col-md-12">
                                        <x-forms.text :fieldLabel="__('superadmin.packages.stripeAnnualPlanId')"
                                                    :fieldValue="$package->stripe_annual_plan_id"
                                                    fieldName="stripe_annual_plan_id"
                                                    fieldId="stripe_annual_plan_id"/>
                                    </div>
                                @endif
                                @if($paymentGateway->razorpay_status == 'active')
                                    <div class="col-md-12">
                                        <x-forms.text :fieldLabel="__('superadmin.packages.razorpayAnnualPlanId')"
                                                    :fieldValue="$package->razorpay_annual_plan_id"
                                                    fieldName="razorpay_annual_plan_id"
                                                    fieldId="razorpay_annual_plan_id"/>
                                    </div>
                                @endif
                                @if($paymentGateway->paystack_status == 'active')
                                    <div class="col-md-12">
                                        <x-forms.text :fieldLabel="__('superadmin.packages.paystackAnnualPlanId')"
                                                    :fieldValue="$package->paystack_annual_plan_id"
                                                    fieldName="paystack_annual_plan_id"
                                                    fieldId="paystack_annual_plan_id"/>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                @else
                <input type="hidden" name="currency_id" value="{{ $global->currency_id }}">
                @endif

                @if(isset($trial))
                    <h4 class="mt-3 p-3 f-21 font-weight-normal text-capitalize border-top-grey">
                        @lang('superadmin.trialPackageSettings')
                    </h4>
                    <div class="row px-3">
                        <div class="col-lg-3 ">
                            <x-forms.number :fieldLabel="__('superadmin.packages.trialPeriod').'('.__('app.days').')'"
                                            fieldName="no_of_days"
                                            fieldId="no_of_days"
                                            :popover="__('superadmin.packages.trialPeriodInfo')"
                                            :fieldValue="$trial->no_of_days"/>
                        </div>

                        <div class="col-lg-3 ">
                            <x-forms.number :fieldLabel="__('superadmin.packages.notificationBeforeDays')"
                                            fieldName="notification_before"
                                            fieldId="notification_before"
                                            :popover="__('superadmin.packages.notificationBeforeDaysInfo')"
                                            :fieldValue="$trial->notification_before"/>
                        </div>

                        <div class="col-lg-6 ">
                            <x-forms.text :fieldLabel="__('superadmin.packages.trialMessageTitle')" fieldName="trial_message"
                                          fieldId="trial_message"
                                          :popover="__('superadmin.packages.trialMessageInfo')"
                                          :fieldValue="$trial->trial_message"/>
                        </div>
                    </div>
                @endif

                <h4 class="mt-3 p-3 f-21 font-weight-normal text-capitalize border-top-grey">
                    @lang('superadmin.packages.selectModule')
                </h4>
                <div class="row px-3">
                    <div class="col-md-12 border-bottom-grey mb-2 pb-2">
                        <x-forms.checkbox class="mr-0 mr-lg-2 mr-md-2 select_all_permission"
                                          :fieldLabel="__('modules.permission.selectAll')" fieldName=""
                                          fieldId="select_all_permission"/>
                    </div>
                    @php
                        $moduleInPackage = (array)json_decode($package->module_in_package);
                    @endphp
                    @foreach($packageModules as $module)
                        <div class="col-md-2">
                            <x-forms.checkbox class="mr-0 mr-lg-2 mr-md-2 module_checkbox"
                                              :fieldLabel=" __('modules.module.'.$module->module_name)"
                                              :checked="isset($moduleInPackage) && in_array($module->module_name, $moduleInPackage)"
                                              fieldName="module_in_package[{{ $module->id }}]"
                                              :fieldId="$module->module_name" :fieldValue="$module->module_name"/>
                        </div>
                    @endforeach
                </div>
                <input type="hidden" name="unchecked_value" id="unchecked_value">

                <div class="row p-3">
                    @if($package->default == 'no' || $package->default == 'lifetime')
                        <div class="col-md-12">
                            <x-forms.textarea :fieldLabel="__('app.description')" fieldName="description"
                                              :fieldValue="$package->description" fieldId="description"/>
                        </div>
                    @endif

                    @if(isset($trial))
                        <div class="col-md-3">
                            <x-forms.select fieldId="status" :fieldLabel="__('app.status')"
                                            fieldName="status">
                                <option value="active"
                                        @if($trial->status == 'active') selected @endif>@lang('app.active')</option>
                                <option value="inactive"
                                        @if($trial->status == 'inactive') selected @endif>@lang('app.inactive')</option>
                            </x-forms.select>
                        </div>
                    @endif
                </div>


                <x-form-actions>
                    <x-forms.button-primary class="mr-3" id="update-package-form" icon="check">@lang('app.update')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('superadmin.packages.index')"
                                           class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>

            </div>
        </x-form>

    </div>
</div>


<script>

    $(document).ready(function () {
        $(".select-picker").selectpicker();

        $('.select_all_permission').change(function () {
            if ($(this).is(':checked')) {
                $('.module_checkbox').prop('checked', true);
            } else {
                $('.module_checkbox').prop('checked', false);
            }
        });

        $('input[type=radio][name=package_type]').change(function() {
            let packageType = $('#package_type').val();

            if (this.value == 'free') {
                $('.payment-title').addClass('d-none');
                $('.payment-box').addClass('d-none');
                $('.lifetime_price_section').addClass('d-none');

            } else if (this.value == 'paid') {

                if (packageType === 'standard') {
                    $('.payment-title').removeClass('d-none');
                    $('.payment-box').removeClass('d-none');
                    $('.lifetime_price_section').addClass('d-none');
                } else if (packageType === 'lifetime') {
                    $('.payment-title').addClass('d-none');
                    $('.payment-box').addClass('d-none');
                    $('.lifetime_price_section').removeClass('d-none');
                }
            }
        });

        $('#package_type').on('change', function() {
            let selectedValue = $(this).val();
            let packageType = $('input[type=radio][name=package_type]:checked').val();



            if (packageType === 'free') {
                $('.payment-title').addClass('d-none');
                $('.payment-box').addClass('d-none');
                $('.lifetime_price_section').addClass('d-none');
            }
            else if (packageType === 'paid') {

                if (selectedValue === 'standard') {
                    $('.payment-title').removeClass('d-none');
                    $('.payment-box').removeClass('d-none');
                    $('.lifetime_price_section').addClass('d-none');
                    $('.monthly_package').removeClass('d-none');
                    $('.annual_package').removeClass('d-none');

                } else if (selectedValue === 'lifetime') {
                    $('.payment-title').addClass('d-none');
                    $('.payment-box').addClass('d-none');
                    $('.lifetime_price_section').removeClass('d-none');
                }
            }
        });

        $('.packages').change(function () {
            var plan = $(this).data('value');
            if (plan == 'monthly') {
                if ($(this).is(':checked')) {
                    $('.monthly_package').removeClass('d-none');
                } else {
                    $('.monthly_package').addClass('d-none');
                }
            } else if (plan == 'annual') {
                if ($(this).is(':checked')) {
                    $('.annual_package').removeClass('d-none');
                } else {
                    $('.annual_package').addClass('d-none');
                }
            }
        });
        $('#update-package-form').click(function () {
            uncheckedValues = [];
            $('.module_checkbox').each(function() {
                if (!$(this).is(':checked')) {
                    var uncheckedValue = $(this).val();
                    uncheckedValues.push(uncheckedValue);
                }
            });
            var uncheckedValuesString = uncheckedValues.join(',');
            $('#unchecked_value').val(uncheckedValuesString);
            $.easyAjax({
                url: "{{ route('superadmin.packages.update', [$package->id]) }}",
                container: '#update-package-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#update-package-form",
                data: $('#update-package-data-form').serialize(),
                success: function () {
                    showTable();
                }
            });
        });

        const showTable = () => {
            try {
                window.LaravelDataTables["package-table"].draw();

            } catch (err) {
            }
        }


        init(RIGHT_MODAL);
    });

</script>
