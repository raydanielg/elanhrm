<div class="col-xl-12 col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-20">
    <input type="hidden" name="language_setting_id" value="{{ $lang->id }}">

    <div class="row">
        <div class="col-md-12">
            <x-forms.text :fieldLabel="__('app.title').$lang->label" fieldName="title" :fieldValue="$trFrontDetail ? $trFrontDetail->cta_title : ''" autocomplete="off" fieldId="title" />
        </div>
        <div class="col-md-12">
            <x-forms.textarea fieldId="detail" :fieldLabel="__('app.description').$lang->label" fieldName="detail" :fieldValue="$trFrontDetail ? $trFrontDetail->cta_detail : ''" >
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
