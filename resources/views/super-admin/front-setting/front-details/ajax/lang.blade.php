<div class="col-xl-12 col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-20">
    <input type="hidden" name="language_setting_id" value="{{ $lang->id }}">

    <div class="row">
        <div class="col-md-12">
            <x-forms.text :fieldLabel="__('superadmin.frontCms.headerTitle').$lang->label" fieldName="header_title" :fieldValue="$trFrontDetail->header_title ?? ''" autocomplete="off" fieldId="header_title" />
        </div>

        <div class="col-md-12">
            <div class="form-group my-3">
                <x-forms.label fieldId="header_description" :fieldLabel="__('app.description').$lang->label">
                </x-forms.label>
                <div id="header_description">{!!  $trFrontDetail->header_description ?? '' !!}</div>
                <textarea name="header_description" id="header_description_text" class="d-none"></textarea>
            </div>
        </div>
        <div class="col-md-12">
            <x-forms.file allowedFileExtensions="png jpg jpeg svg" class="mr-0 mr-lg-2 mr-md-2 cropper"
                :fieldLabel="__('superadmin.frontCms.mainImage')"
                :fieldValue="($trFrontDetail->image_url ?? '')" fieldName="image"
                fieldId="image" :popover="__('superadmin.headerImageSizeMessage')">
            </x-forms.file>
        </div>

        @if ($global->front_design == 0)
            <div class="col-12 p-0 mt-4">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-top-grey">@lang('superadmin.frontCms.featureDetail')</h4>
            </div>

            <div class="col-lg-12">
                <x-forms.text :fieldLabel="__('superadmin.frontCms.featureTitle')" fieldName="feature_title" :fieldValue="$trFrontDetail->feature_title ?? ''" fieldId="feature_title" fieldRequired="true"/>
            </div>

            <div class="col-lg-12">
                <x-forms.textarea fieldId="feature_description" :fieldLabel="__('superadmin.frontCms.featureDescription')" fieldName="feature_description" :fieldValue="$trFrontDetail->feature_description ?? ''" fieldRequired="true">
                </x-forms.textarea>
            </div>
            <div class="col-12 p-0">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-top-grey">@lang('superadmin.frontCms.priceDetail')</h4>
            </div>

            <div class="col-lg-12">
                <x-forms.text :fieldLabel="__('superadmin.frontCms.priceTitle')" fieldName="price_title" :fieldValue="$trFrontDetail->price_title ?? ''" fieldId="price_title" fieldRequired="true"/>
            </div>

            <div class="col-lg-12">
                <x-forms.textarea fieldId="price_description" :fieldLabel="__('superadmin.frontCms.priceDescription')" fieldName="price_description" :fieldValue="$trFrontDetail->price_description ?? ''" fieldRequired="true">
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
<script>
    $(document).ready(function () {
        quillImageLoad('#header_description');
    });
</script>

