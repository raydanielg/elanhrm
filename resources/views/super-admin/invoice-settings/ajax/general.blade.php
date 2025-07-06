<link rel="stylesheet" href="{{ asset('vendor/css/image-picker.min.css') }}">

<div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-4 ">
    @method('PUT')

    <div class="row">

        <div class="col-lg-6">
            <x-forms.file allowedFileExtensions="png jpg jpeg svg" class="mr-0 mr-lg-2 mr-md-2"
                          :fieldLabel="__('modules.invoiceSettings.logo')"
                          fieldName="logo" fieldId="logo" :fieldValue="$invoiceSetting->logo_url"
                          :popover="__('messages.invoiceLogoTooltip')"/>
        </div>
        <div class="col-lg-6">
            <x-forms.file allowedFileExtensions="png jpg jpeg svg" class="mr-0 mr-lg-2 mr-md-2"
                          :fieldLabel="__('modules.invoiceSettings.authorisedSignatorySignature')"
                          fieldName="authorised_signatory_signature" fieldId="authorised_signatory_signature"
                          :fieldValue="$invoiceSetting->authorised_signatory_signature_url"
                          :popover="__('messages.authorisedSignatorySignatureTooltip')"/>
        </div>

        <div class="col-lg-6">
            <x-forms.select fieldId="locale" :fieldLabel="__('modules.accountSettings.language')"
                            fieldName="locale" search="true">
                @foreach ($languageSettings as $language)
                    <option {{ global_setting()->locale == $language->language_code ? 'selected' : '' }}
                            data-content="<span class='flag-icon flag-icon-{{ ($language->flag_code == 'en') ? 'gb' : strtolower($language->flag_code) }} flag-icon-squared'></span> {{ $language->language_name }}"
                            @if ($invoiceSetting->locale == $language->language_code) selected
                            @endif value="{{ $language->language_code }}">
                        {{ $language->language_name }}</option>
                @endforeach
            </x-forms.select>
        </div>

        <div class="col-lg-4 mt-5">
            <x-forms.checkbox :checked="$invoiceSetting->authorised_signatory==1"
                              :fieldLabel="__('app.showAuthorisedSignatory')"
                              fieldName="show_authorised_signatory" fieldId="show_authorised_signatory"
                              :popover="__('messages.invoiceAuthorisedSignatoryShowTooltip')"/>
        </div>

        <div class="col-lg-12 mt-4">
            <div class="form-group">
                <x-forms.label fieldId="template" :fieldLabel="__('modules.invoiceSettings.template')"
                               fieldRequired="true">
                </x-forms.label>
                <select name="template" class="image-picker show-labels show-html">
                    <option data-img-src="{{ asset('img/invoice-template/1.png') }}"
                            @if ($invoiceSetting->template == 'invoice-1') selected @endif
                            value="invoice-1">@lang('modules.invoiceSettings.template') 1
                    </option>
                    <option data-img-src="{{ asset('img/invoice-template/2.png') }}"
                            @if ($invoiceSetting->template == 'invoice-2') selected @endif
                            value="invoice-2">@lang('modules.invoiceSettings.template') 2
                    </option>
                    <option data-img-src="{{ asset('img/invoice-template/3.png') }}"
                            @if ($invoiceSetting->template == 'invoice-3') selected @endif
                            value="invoice-3">@lang('modules.invoiceSettings.template') 3
                    </option>
                    <option data-img-src="{{ asset('img/invoice-template/4.png') }}"
                            @if ($invoiceSetting->template == 'invoice-4') selected @endif
                            value="invoice-4">@lang('modules.invoiceSettings.template') 4
                    </option>
                    <option data-img-src="{{ asset('img/invoice-template/5.png') }}"
                            @if ($invoiceSetting->template == 'invoice-5') selected @endif
                            value="invoice-5">@lang('modules.invoiceSettings.template') 5
                    </option>
                </select>
            </div>
        </div>

        <div class="col-lg-4 mt-3">
            <div class="form-group my-3">
                <x-forms.text class="mr-0 mr-lg-2 mr-md-2"
                                  :fieldLabel="__('superadmin.billing_name')"
                                  fieldName="billing_name"
                                  fieldId="billing_name"
                                  :fieldValue="$invoiceSetting->billing_name">
                </x-forms.text>
            </div>
        </div> <div class="col-lg-4 mt-3">
            <div class="form-group my-3">
                <x-forms.text class="mr-0 mr-lg-2 mr-md-2"
                                  :fieldLabel="__('superadmin.billing_tax_name')"
                                  fieldName="billing_tax_name"
                                  fieldId="billing_tax_name"
                                  :fieldValue="$invoiceSetting->billing_tax_name">
                </x-forms.text>
            </div>
        </div> <div class="col-lg-4 mt-3">
            <div class="form-group my-3">
                <x-forms.text class="mr-0 mr-lg-2 mr-md-2"
                                  :fieldLabel="__('superadmin.billing_tax_id')"
                                  fieldName="billing_tax_id"
                                  fieldId="billing_tax_id"
                                  :fieldValue="$invoiceSetting->billing_tax_id">
                </x-forms.text>
            </div>
        </div>

        <div class="col-lg-12 mt-3">
            <div class="form-group my-3">
                <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2"
                                  :fieldLabel="__('superadmin.billing_address')"
                                  fieldName="billing_address"
                                  fieldId="billing_address"
                                  :fieldPlaceholder="__('superadmin.billing_address')"
                                  :fieldValue="$invoiceSetting->billing_address">
                </x-forms.textarea>
            </div>
        </div>

        <div class="col-lg-12 mt-3">
            <div class="form-group my-3">
                <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2"
                                  :fieldLabel="__('modules.invoiceSettings.invoiceTerms')"
                                  fieldName="invoice_terms"
                                  fieldId="invoice_terms"
                                  :fieldPlaceholder="__('placeholders.invoices.invoiceTerms')"
                                  :fieldValue="$invoiceSetting->invoice_terms">
                </x-forms.textarea>
            </div>
        </div>

    </div>

</div>


<!-- Buttons Start -->
<div class="w-100 border-top-grey">
    <x-setting-form-actions>
        <x-forms.button-primary id="save-form" class="mr-3" icon="check">@lang('app.save')
        </x-forms.button-primary>
    </x-setting-form-actions>
</div>
<!-- Buttons End -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/image-picker/0.3.1/image-picker.min.js"></script>
<script>
    // Initializing image picker
    $('.image-picker').imagepicker();

    // save invoice setting
    $('#save-form').click(function () {
        $.easyAjax({
            url: "{{ route('superadmin.settings.global-invoice-settings.update', $invoiceSetting->id) }}",
            container: '#editSettings',
            type: "POST",
            redirect: true,
            file: true,
            data: $('#editSettings').serialize(),
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-form",
        })
    });
</script>
