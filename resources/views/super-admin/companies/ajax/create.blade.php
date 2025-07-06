<div class="row">
    <div class="col-sm-12">
        <x-form id="save-company-data-form">
            @include('sections.password-autocomplete-hide')

            <div class="add-company bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('modules.client.companyDetails')</h4>
                <div class="row p-20">
                    @includeFirst(['subdomain::super-admin.company.create', 'super-admin.subdomain.alert'])
                    <div class="col-lg-9 col-xl-10">
                        <div class="row">
                            <div class="col-md-4">
                                <x-forms.text fieldId="company_name"
                                              :fieldLabel="__('modules.accountSettings.companyName')"
                                              fieldName="company_name"
                                              fieldRequired="true"
                                              :fieldPlaceholder="__('placeholders.company')">
                                </x-forms.text>
                            </div>
                            <div class="col-md-4">
                                <x-forms.email fieldId="company_email"
                                               :fieldLabel="__('modules.accountSettings.companyEmail')"
                                               fieldRequired="true"
                                               fieldName="company_email" :fieldPlaceholder="__('placeholders.email')">
                                </x-forms.email>
                            </div>
                            <div class="col-md-4">
                                <x-forms.tel fieldId="company_phone"
                                             :fieldLabel="__('modules.accountSettings.companyPhone')"
                                             fieldName="company_phone"
                                             :fieldPlaceholder="__('placeholders.mobile')"></x-forms.tel>
                            </div>

                            <div class="col-md-4">
                                <x-forms.url fieldId="website"
                                              :fieldLabel="__('modules.accountSettings.companyWebsite')"
                                              fieldName="website"
                                              :fieldPlaceholder="__('placeholders.website')">
                                </x-forms.url>
                            </div>

                            <div class="col-md-4">
                                <x-forms.select fieldId="currency_id"
                                                :fieldLabel="__('modules.accountSettings.defaultCurrency')"
                                                fieldName="currency_id">
                                    @foreach ($currencies as $currency)
                                        <option @selected($currency->currency_code == $globalCurrenyCode) value="{{ $currency->id }}">
                                            {{ $currency->currency_code . ' (' . $currency->currency_symbol . ')' }}
                                        </option>
                                    @endforeach
                                </x-forms.select>
                            </div>

                            <div class="col-md-4">
                                <x-forms.select search fieldId="timezone"
                                                :fieldLabel="__('modules.accountSettings.defaultTimezone')"
                                                fieldName="timezone">
                                    @foreach($timezones as $tz)
                                        <option @selected($tz == global_setting()->timezone)  value="{{ $tz }}">{{ $tz }}</option>
                                    @endforeach
                                </x-forms.select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-xl-2">
                        <x-forms.file allowedFileExtensions="png jpg jpeg svg" class="mr-0 mr-lg-2 mr-md-2 cropper"
                                      :fieldLabel="__('modules.accountSettings.companyLogo')" fieldName="logo"
                                      fieldId="logo"
                                      fieldHeight="119" :popover="__('messages.fileFormat.ImageFile')"/>
                    </div>

                    <div class="col-md-3">
                        <x-forms.label class="mt-3" fieldId="category"
                                       :fieldLabel="__('modules.accountSettings.language')">
                        </x-forms.label>
                        <x-forms.input-group>
                            <select class="form-control select-picker" name="locale" id="locale"
                                    data-live-search="true">
                                @foreach($languageSettings as $language)
                                    <option data-content="<span class='flag-icon flag-icon-{{ ($language->flag_code == 'en') ? 'gb' : strtolower($language->flag_code) }} flag-icon-squared'></span> {{ $language->language_name }}"
                                            @selected($language->language_code == global_setting()->locale)
                                            value="{{ $language->language_code }}">{{ $language->language_name }}</option>
                                @endforeach
                            </select>
                        </x-forms.input-group>
                    </div>

                    <div class="col-md-3">
                        <x-forms.select fieldId="status" :fieldLabel="__('app.status')"
                                        fieldName="status">
                            <option value="active">@lang('app.active')</option>
                            <option value="inactive">@lang('app.inactive')</option>
                        </x-forms.select>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2"
                                              :fieldLabel="__('modules.accountSettings.companyAddress')"
                                              fieldName="address"
                                              fieldId="address" :fieldPlaceholder="__('placeholders.address')">
                            </x-forms.textarea>
                        </div>
                    </div>
                </div>

                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                    @lang('superadmin.accountDetails')</h4>
                <div class="row p-20">
                    <div class="col-md-6">
                        <x-forms.text fieldId="name" :fieldLabel="__('app.name')" fieldName="name"
                                      fieldRequired="true"
                                      :fieldPlaceholder="__('placeholders.name')"></x-forms.text>
                    </div>
                    <div class="col-md-6">
                        <x-forms.email
                            fieldId="email" :fieldLabel="__('app.email').' ( '.__('messages.loginDetailsEmailed').' )'"
                            fieldName="email"
                            fieldRequired="true"
                            :popover="__('superadmin.emailInfoCompany')"
                            :fieldPlaceholder="__('placeholders.email')">
                        </x-forms.email>
                    </div>
                </div>

                <x-forms.custom-field :fields="$fields"></x-forms.custom-field>
                <x-form-actions>
                    <x-forms.button-primary id="save-company-form" class="mr-3" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('superadmin.companies.index')"
                                           class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>

    </div>
</div>


<script>
    $(document).ready(function () {

        $('.custom-date-picker').each(function(ind, el) {
            datepicker(el, {
                position: 'bl',
                ...datepickerConfig
            });
        });

        $('#save-company-form').click(function () {
            const url = "{{ route('superadmin.companies.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-company-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-company-form",
                file: true,
                data: $('#save-company-data-form').serialize()
            })
        });

        $('#random_password').click(function () {
            const randPassword = Math.random().toString(36).substr(2, 8);

            $('#password').val(randPassword);
        });

        $('.cropper').on('dropify.fileReady', function (e) {
            var inputId = $(this).find('input').attr('id');
            var url = "{{ route('cropper', ':element') }}";
            url = url.replace(':element', inputId);
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        init(RIGHT_MODAL);
    });
</script>
@includeIf('subdomain::super-admin.company.script')
