<div class="col-xl-12 col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-20">
    <input type="hidden" name="language_setting_id" value="{{ $lang->id }}">

    <div class="row">
        <div class="col-md-3">
            <x-forms.text :fieldLabel="__('modules.attendance.home')" fieldName="home" :fieldValue="$frontMenu->home" autocomplete="off" fieldId="home" />
        </div>
        <div class="col-md-3">
            <x-forms.text :fieldLabel="__('app.price')" fieldName="price" :fieldValue="$frontMenu->price" autocomplete="off" fieldId="price" />
        </div>
        <div class="col-md-3">
            <x-forms.text :fieldLabel="__('app.contact')" fieldName="contact" :fieldValue="$frontMenu->contact" autocomplete="off" fieldId="contact" />
        </div>
        <div class="col-md-3">
            <x-forms.text :fieldLabel="__('superadmin.feature')" fieldName="feature" :fieldValue="$frontMenu->feature" autocomplete="off" fieldId="feature" />
        </div>
        <div class="col-md-3">
            <x-forms.text :fieldLabel="__('superadmin.frontCms.getStarted')" fieldName="get_start" :fieldValue="$frontMenu->get_start" autocomplete="off" fieldId="get_start" />
        </div>
        <div class="col-md-3">
            <x-forms.text :fieldLabel="__('app.login')" fieldName="login" :fieldValue="$frontMenu->login" autocomplete="off" fieldId="login" />
        </div>
        <div class="col-md-3">
            <x-forms.text :fieldLabel="__('superadmin.menu.contactSubmit')" fieldName="contact_submit" :fieldValue="$frontMenu->contact_submit" autocomplete="off" fieldId="contact_submit" />
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
