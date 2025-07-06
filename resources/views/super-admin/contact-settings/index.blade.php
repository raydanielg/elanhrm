@extends('layouts.app')
@section('content')
    <!-- SETTINGS START -->
    <div class="w-100 d-flex">

        <x-super-admin.front-setting-sidebar :activeMenu="$activeSettingMenu" />

        <x-setting-card>

            <x-slot name="header">
                <div class="s-b-n-header" id="tabs">
                    <h2 class="f-21 font-weight-normal text-capitalize border-bottom-grey mb-0 p-20">
                        @lang($pageTitle)</h2>
                </div>
            </x-slot>

            <!-- LEAVE SETTING START -->
            <div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-4">
                <div class="row">
                    <div class="col-md-6">
                        <x-forms.email :fieldLabel="__('app.email')" fieldName="email" :fieldValue="$frontDetail->email" autocomplete="off" fieldId="email"  fieldPlaceholder=""/>
                    </div>

                    <div class="col-md-6">
                        <x-forms.tel :fieldLabel="__('app.phone')" fieldName="phone" :fieldValue="$frontDetail->phone" autocomplete="off" fieldId="phone" fieldPlaceholder=""/>
                    </div>
                    <div class="col-md-12">
                        <x-forms.textarea fieldId="address" :fieldLabel="__('app.address')" fieldName="address" :fieldValue="$frontDetail->address" >
                        </x-forms.textarea>
                    </div>
                    <div class="col-md-12">
                        <x-forms.textarea fieldId="contact_html" :fieldLabel="__('superadmin.htmlOrEmbeded')" fieldName="contact_html" :fieldValue="$frontDetail->contact_html" >
                        </x-forms.textarea>
                    </div>
                </div>
            </div>
            <!-- LEAVE SETTING END -->

            <x-slot name="action">
                <!-- Buttons Start -->
                <div class="w-100 border-top-grey">
                    <x-setting-form-actions>
                        <x-forms.button-primary id="save-form" class="mr-3" icon="check">@lang('app.update')
                        </x-forms.button-primary>
                    </x-setting-form-actions>
                </div>
                <!-- Buttons End -->
            </x-slot>
        </x-setting-card>

    </div>
    <!-- SETTINGS END -->
@endsection

@push('scripts')
    <script>

        $('#save-form').click(function() {
            $.easyAjax({
                url: "{{ route('superadmin.front-settings.contact_settings') }}",
                container: '#editSettings',
                blockUI: true,
                type: "POST",
                data: $('#editSettings').serialize()
            })
        });
    </script>
@endpush
