<div class="row">
    <div class="col-sm-12">
        <x-form id="update-company-data-form" method="PUT">
            @include('sections.password-autocomplete-hide')

            <div class="add-company bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('modules.client.companyDetails')</h4>
                <div class="row p-20">
                    @includeFirst(['subdomain::super-admin.company.edit', 'super-admin.subdomain.alert'])
                    <div class="col-lg-9 col-xl-10">
                        <div class="row">
                            <div class="col-md-4">
                                <x-forms.text fieldId="company_name"
                                              :fieldLabel="__('modules.accountSettings.companyName')"
                                              fieldName="company_name" :fieldValue="$company->company_name"
                                              fieldRequired="true"
                                              :fieldPlaceholder="__('placeholders.company')">
                                </x-forms.text>
                            </div>

                            <div class="col-md-4">
                                <x-forms.email fieldId="company_email"
                                               :fieldLabel="__('modules.accountSettings.companyEmail')"
                                               fieldName="company_email" :fieldValue="$company->company_email"
                                               fieldRequired="true"
                                               :fieldPlaceholder="__('placeholders.email')">
                                </x-forms.email>
                            </div>
                            <div class="col-md-4">
                                <x-forms.tel fieldId="company_phone"
                                             :fieldLabel="__('modules.accountSettings.companyPhone')"
                                             fieldName="company_phone" :fieldValue="$company->company_phone"
                                             fieldPlaceholder="e.g. 987654321"></x-forms.tel>
                            </div>

                            <div class="col-md-4">
                                <x-forms.text fieldId="website"
                                              :fieldLabel="__('modules.accountSettings.companyWebsite')"
                                              fieldName="website" :fieldValue="$company->website"
                                              fieldPlaceholder="e.g. https://www.spacex.com/">
                                </x-forms.text>
                            </div>

                            <div class="col-md-4">
                                <x-forms.select fieldId="currency_id"
                                                :fieldLabel="__('modules.accountSettings.defaultCurrency')"
                                                fieldName="currency_id">
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}"
                                                @if($currency->id == $company->currency_id) selected @endif>
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
                                        <option @if($company->timezone == $tz) selected @endif  value="{{ $tz }}">{{ $tz }}</option>
                                    @endforeach
                                </x-forms.select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-xl-2">
                        <x-forms.file allowedFileExtensions="png jpg jpeg svg" class="mr-0 mr-lg-2 mr-md-2 cropper"
                                      :fieldLabel="__('modules.accountSettings.companyLogo')" fieldName="logo"
                                      fieldId="logo" :fieldValue="$company->masked_logo_url"
                                      fieldHeight="119" :popover="__('messages.fileFormat.ImageFile')"/>
                    </div>

                    <div class="col-md-4">
                        <x-forms.label class="mt-3" fieldId="category"
                                       :fieldLabel="__('modules.accountSettings.language')">
                        </x-forms.label>
                        <x-forms.input-group>
                            <select class="form-control select-picker" name="locale" id="locale"
                                    data-live-search="true">

                                @foreach ($languageSettings as $language)
                                    <option {{ $company->locale == $language->language_code ? 'selected' : '' }}
                                            data-content="<span class='flag-icon flag-icon-{{ ($language->flag_code == 'en') ? 'gb' : strtolower($language->flag_code) }} flag-icon-squared'></span> {{ $language->language_name }}"
                                            value="{{ $language->language_code }}">{{ $language->language_name }}
                                    </option>
                                @endforeach

                            </select>
                        </x-forms.input-group>
                    </div>

                    <div class="col-md-4">
                        <x-forms.select fieldId="status" :fieldLabel="__('app.status')"
                                        fieldName="status">
                            <option value="active"
                                    @if($company->status == 'active') selected @endif>@lang('app.active')</option>
                            <option value="inactive"
                                    @if($company->status == 'inactive') selected @endif>@lang('app.inactive')</option>
                            <option value="license_expired"
                                    @if($company->status == 'license_expired') selected @endif>@lang('superadmin.dashboard.licenseExpired')</option>
                        </x-forms.select>
                    </div>

                    @if (global_setting()->company_need_approval)
                        <div class="col-md-4">
                            <x-forms.select fieldId="approved" :fieldLabel="__('app.approved')"
                                            fieldName="approved">
                                <option value="1"
                                        @if($company->approved == 1) selected @endif>@lang('app.yes')</option>
                                <option value="0"
                                        @if($company->approved == 0) selected @endif>@lang('app.no')</option>
                            </x-forms.select>
                        </div>
                    @endif

                    <div class="col-md-6">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2"
                                              :fieldLabel="__('modules.accountSettings.companyAddress')"
                                              :fieldValue="$company->defaultAddress->address" fieldName="address"
                                              fieldRequired="true"
                                              fieldId="address" :fieldPlaceholder="__('placeholders.address')">
                            </x-forms.textarea>
                        </div>
                    </div>
                </div>

                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-top-grey">
                    @lang('superadmin.accountDetails')</h4>
                <div class="row p-20">
                    <div class="col">
                        @if($company->user)
                            <x-cards.user :image="$company->user->image_url">
                                <div class="row">
                                    <div class="col-10">
                                        <h4 class="card-title f-15 f-w-500 text-darkest-grey mb-0">
                                            {{ ($company->user->salutation ? $company->user->salutation->label() . ' ' : '') . $company->user->name }}
                                            @if(!is_null($company->user->country_id))
                                                <x-flag :country="$company->user->country"/>
                                            @endisset
                                        </h4>
                                    </div>
                                </div>

                                <p class="f-13 font-weight-normal text-dark-grey mb-0">
                                    {{ $company->user->email }}
                                </p>

                                @if ($company->user->status == 'active')
                                    <p class="card-text f-12 text-lightest">@lang('app.lastLogin')

                                        @if (!is_null($company->user->last_login))
                                            {{ $company->user->last_login->timezone(global_setting()->timezone)->format(global_setting()->date_format . ' ' . global_setting()->time_format) }}
                                        @else
                                            --
                                        @endif
                                    </p>

                                @else
                                    <p class="card-text f-12 text-lightest">
                                        <x-status :value="__('app.inactive')" color="red"/>
                                    </p>
                                @endif
                            </x-cards.user>
                        @else
                            <x-cards.user :image="'https://www.gravatar.com/avatar/noimage.png?s=200&d=mp'">
                                <div class="card-text f-12 text-lightest m-t-5">There is no active company admin for
                                    this company
                                </div>
                            </x-cards.user>
                        @endif
                    </div>

                </div>

                <x-forms.custom-field :fields="$fields" :model="$company"></x-forms.custom-field>

                <x-form-actions>
                    <x-forms.button-primary id="update-company-form" class="mr-3" icon="check">@lang('app.save')
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

        $('#update-company-form').click(function () {
            const url = "{{ route('superadmin.companies.update', [$company->id])}}";

            $.easyAjax({
                url: url,
                container: '#update-company-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#update-company-form",
                file: true,
                data: $('#update-company-data-form').serialize()
            })
        });

        $('#random_password').click(function () {
            const randPassword = Math.random().toString(36).substr(2, 8);

            $('#password').val(randPassword);
        });

        init(RIGHT_MODAL);
    });
</script>
@includeIf('subdomain::super-admin.company.script')
