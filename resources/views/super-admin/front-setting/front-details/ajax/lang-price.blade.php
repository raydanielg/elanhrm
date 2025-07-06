<div class="col-xl-12 col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-20">
    <input type="hidden" name="language_setting_id" value="{{ $lang->id }}">

    <div class="row">
        <div class="col-md-12">
            <x-forms.text :fieldLabel="__('superadmin.frontCms.priceTitle').$lang->label" fieldName="price_title"
                          :fieldRequired="true"
                          :fieldValue="$trFrontDetail->price_title ?? ''" autocomplete="off" fieldId="price_title
"/>
        </div>
        <div class="col-md-12">
            <x-forms.textarea fieldId="price_description"
                              :fieldRequired="true"
                              :fieldLabel="__('superadmin.frontCms.priceDescription').$lang->label"
                              fieldName="price_description" :fieldValue="$trFrontDetail->price_description ?? ''">
            </x-forms.textarea>
        </div>
    </div>
</div>
<!-- Buttons Start -->
<div class="w-100 border-top-grey">
    <x-setting-form-actions>
        <div class="d-flex">
            <x-forms.button-primary class="mr-3 w-100" icon="check" id="saveFrontSetting">@lang('app.update')
            </x-forms.button-primary>
        </div>
    </x-setting-form-actions>
</div>
