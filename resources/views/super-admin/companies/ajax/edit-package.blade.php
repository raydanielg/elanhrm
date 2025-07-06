<div class="row">
    <div class="col-sm-12">
        <x-form id="update-company-package-form" method="PUT">

            <input type="hidden" name="request_from" value="{{ $pageInfo }}">

            <div class="add-company bg-white rounded">
                @if ($latestInvoice)
                    <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">{{__('superadmin.companyCurrentPackage')}}</h4>
                    <div class="row p-20 border-bottom-grey">
                        <div class="col-12">
                            <x-cards.data-row :label="__('superadmin.package')" :value="$company->package->name ?? '--'" />
                            @if ($company->package->default != 'trial' && $company->package->default != 'lifetime')
                                <x-cards.data-row :label="__('superadmin.packageType')" :value="__('superadmin.' . $company->package_type) ?? '--'" />
                                <x-cards.data-row :label="__('app.amount')" :value="$currency->currency_symbol . $latestInvoice->total ?? '--'" />
                                <x-cards.data-row :label="__('superadmin.paymentDate')" :value="$latestInvoice->pay_date?->format($global->date_format) ?? '--'" />
                                <x-cards.data-row :label="__('superadmin.nextPaymentDate')" :value="$latestInvoice->next_pay_date?->format($global->date_format) ?? '--'" />
                            @endif
                            <x-cards.data-row :label=" $company->package->default == 'trial' ? __('superadmin.packages.trialExpiresOn') : __('superadmin.packages.licenseExpiresOn')" :value="$company->licence_expire_on?->format($global->date_format) ?? '--'" />
                        </div>
                    </div>
                @endif

                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">@lang('app.change') @lang('superadmin.company') {{__('superadmin.package')}}</h4>
                <div class="row p-20">
                    <div class="col-md-12 mb-2">
                        <x-company :company="$company" />
                    </div>
                    <div class="col-md-4">
                        <x-forms.select fieldId="package" :fieldLabel="__('superadmin.packages.packages')" search
                                        fieldName="package">
                            <option value=""> @lang('superadmin.packages.selectPackage')</option>
                            @foreach($allPackages as $package)
                                <option value="{{ $package->id }}"
                                        data-type="{{ $package->type }}"
                                        data-days="{{ $package->days }}"
                                        data-default="{{ $package->default }}"
                                        @if ($package->default == 'trial')
                                            @if ($packageSetting->status == 'inactive')
                                            data-content="{{$package->name}}
                                                <span class='badge badge-pill badge-light border'>{{__('app.inactive')}}</span>"
                                            @endif
                                        @endif>
                                        {{ $package->name ?? '' }} @if ($package->is_free == 0 && $package->default == 'lifetime' )

                                        {{global_currency_format($package->price, $package->currency_id)}}@endif
                                        @if($package->is_free) ({{__('superadmin.freePlan') }}) @endif
                                        @if($package->default==='no' || $package->default !='lifetime')
                                            @if ($package->type != 'annual')
                                               @lang('app.monthly'): {{global_currency_format($package->monthly_price, $package->currency_id)}}
                                            @else
                                               @if ($package->default != 'lifetime') @lang('app.annually'): @endif  {{global_currency_format($package->annual_price, $package->currency_id)}}
                                            @endif

                                        @endif
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <input type="hidden" name="package_type" id="package_type" value="{{ $company->package_type }}">

                    <div class="col-md-4 payment-field">
                        <x-forms.number fieldId="amount" :fieldLabel="__('app.amount')" fieldName="amount"></x-forms.number>
                    </div>


                    <div class="col-md-4 payment-field">
                        <x-forms.datepicker fieldId="pay_date" fieldRequired="true"
                            :fieldLabel="__('superadmin.paymentDate')" fieldName="pay_date"
                            :fieldPlaceholder="__('placeholders.date')" />
                    </div>

                            <div class="col-md-4 payment-field nextPaydateField">
                                <x-forms.datepicker fieldId="next_pay_date"
                                    :fieldLabel="__('superadmin.nextPaymentDate')" fieldName="next_pay_date"
                                    :fieldPlaceholder="__('placeholders.date')" />
                            </div>

                            <div class="col-md-4 payment-field licenceExpire">
                                <x-forms.text fieldId="licence_expire_on"
                                    :fieldLabel="__('superadmin.packages.licenseExpiresOn')"
                                    fieldName="licence_expire_on"
                                    :fieldPlaceholder="__('placeholders.date')"/>
                            </div>

                    <div class="col-md-4 trial_expire d-none">
                        <x-forms.text fieldId="trial_expire_on"
                            :fieldLabel="__('superadmin.packages.trialExpiresOn')"
                            fieldName="trial_expire_on"
                            :fieldPlaceholder="__('placeholders.date')"/>
                    </div>
                </div>
                <x-form-actions>
                    <x-forms.button-primary id="update-company-package" class="mr-3" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('superadmin.companies.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>


<script>
    $(document).ready(function() {

        if ("{{ $company->package->default }}" === "lifetime") {
            // Hide the payment fields
            $(".nextPaydateField").addClass("d-none");
            $(".licenceExpire").addClass("d-none");
        }
        var packageInfo = @json($packageInfo);
        var payDatepicker = datepicker('#pay_date', {
            position: 'bl',
            minDate: new Date("{{ str_replace('-', '/', now()->translatedFormat('Y-m-d')) }}"),
            onSelect: function(date) {
                updateDates()
            },
            ...datepickerConfig
        });

        var nexPayDatepicker = datepicker('#next_pay_date', {
            position: 'bl',
            minDate: new Date("{{ str_replace('-', '/', now()->translatedFormat('Y-m-d')) }}"),
            onSelect: function(date, instance) {
                $('#licence_expire_on').val(moment(instance).add(7, 'days').format('{{ $global->moment_date_format }}'));
            },

            ...datepickerConfig
        });

            var licenceExpDatepicker = datepicker('#licence_expire_on', {
                position: 'bl',
                minDate: new Date("{{ str_replace('-', '/', now()->translatedFormat('Y-m-d')) }}"),
                ...datepickerConfig
            });


        $('#update-company-package-form').on('change', '#package', function () {
            $('#package_type').val($(this).find(':selected').data('type'));
            console.log(packageInfo[$('#update-company-package-form #package').val()]);
            $('#amount').val(packageInfo[$('#update-company-package-form #package').val()][$(this).find(':selected').data('type')]);
            updateDates();
        });

        function updateLicenceExpireDate(nextPayDate) {
            let endDate = nextPayDate;

            if (endDate.isValid()) {
                endDate = endDate.add(7, 'days');
                $('#licence_expire_on').val(endDate.format('{{ $global->moment_date_format }}'));

                licenceExpDatepicker.setDate(endDate.toDate());
            }
        }

        function updateNextPayDate(endDate) {
            nexPayDatepicker.setDate(endDate.toDate());
            updateLicenceExpireDate(endDate);
        }

        function updateDates() {
            if ($('#pay_date').val() !== '') {
                let startDate = moment($("#pay_date").val(), '{{ $global->moment_date_format }}');
                let endDate = startDate.add($('#package').find(':selected').data('days'), 'days');

                if (endDate.isValid()) {
                    updateNextPayDate(endDate);
                }
            }
        }


        $('#update-company-package').click(function() {
            const url = "{{ route('superadmin.companies.update_package', [$company->id])}}";

            $.easyAjax({
                url: url,
                container: '#update-company-package-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#update-company-package",
                data: $('#update-company-package-form').serialize()
            })
        });

        $('#package').change(function()
        {
            var package = $(this).find(":selected").text();
            var packageDefault = $(this).find(":selected").data("default");
            var package = $.trim(package);

            if(packageDefault == 'trial')
            {
                $('.payment-field').addClass('d-none');
                $('.trial_expire').removeClass('d-none');

                var licenceExpDatepicker = datepicker('#trial_expire_on', {
                    position: 'bl',
                    minDate: new Date("{{ str_replace('-', '/', now()->translatedFormat('Y-m-d')) }}"),
                    dateSelected: new Date("{{ str_replace('-', '/', now()->addDays($packageSetting->no_of_days)->translatedFormat('Y-m-d')) }}"),
                    ...datepickerConfig
                });

            } else if(packageDefault == 'lifetime'){
                $('.nextPaydateField').addClass('d-none');
                $('.licenceExpire').addClass('d-none');
            }
            else
            {
                $('.payment-field').removeClass('d-none');
                $('.trial_expire').addClass('d-none');
            }

        })

        init(RIGHT_MODAL);
    });
</script>
