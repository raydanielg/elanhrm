<div class="col-xl-12 col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-20">
    <input type="hidden" name="language_setting_id" value="{{ $lang->id }}">

    <div class="row">
        <div class="col-md-12">
            <x-forms.text :fieldLabel="__('superadmin.menu.featureTranslation').' '.__('app.title').$lang->label" fieldName="feature_title"
                :fieldValue="$trFrontDetail ? $trFrontDetail->feature_title : ''" autocomplete="off" fieldId="feature_title" />
        </div>

        <div class="col-md-12">
            <x-forms.textarea fieldId="detail" :fieldLabel="__('superadmin.menu.featureTranslation').' '.__('app.description').$lang->label" fieldName="feature_detail"
                :fieldValue="$trFrontDetail ? $trFrontDetail->feature_description : ''" fieldId="feature_detail">
            </x-forms.textarea>
        </div>

        @if (global_setting()->front_design)
            <div class="col-md-12">
                <x-forms.text :fieldLabel="__('superadmin.menu.features').' '.__('superadmin.types.apps').' '.__('app.title').$lang->label" fieldName="feature_app_title"
                    :fieldValue="$trFrontDetail ? $trFrontDetail->favourite_apps_title : ''" autocomplete="off" fieldId="feature_app_title" />
            </div>

            <div class="col-md-12">
                <x-forms.textarea fieldId="detail" :fieldLabel="__('superadmin.menu.features').' '.__('superadmin.types.apps').' '.__('app.description').$lang->label"
                    fieldName="feature_app_detail" :fieldValue="$trFrontDetail ? $trFrontDetail->favourite_apps_detail : ''">
                </x-forms.textarea>
            </div>
        @endif
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
