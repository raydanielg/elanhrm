<div class="modal-header">
    <h5 class="modal-title">@lang('app.addNew') @lang('superadmin.menu.testimonial')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <x-form id="createMethods" method="POST" class="ajax-form">
            <div class="row">
                <div class="col-lg-6">
                    <x-forms.select fieldId="language" :fieldLabel="__('superadmin.frontCms.defaultLanguage')" fieldName="language">
                        @foreach ($languageSettings as $language)
                            <option
                                data-content="<span class='flag-icon flag-icon-{{ $language->flag_code == 'en' ? 'gb' : strtolower($language->flag_code) }} flag-icon-squared'></span> {{ $language->language_name }}"
                                value="{{ $language->id }}">{{ $language->language_name }}</option>
                        @endforeach
                    </x-forms.select>
                </div>
                <div class="col-lg-6">
                    <x-forms.text :fieldLabel="__('app.name')" fieldName="name" autocomplete="off" fieldId="name" fieldRequired="true" />
                </div>

                <div class="col-md-12">
                    <x-forms.textarea fieldId="comment" :fieldLabel="__('app.comment')" fieldName="comment" fieldRequired="true" >
                    </x-forms.textarea>
                </div>

                <div class="col-md-12">
                    <x-forms.number :fieldLabel="__('app.rating')" fieldName="rating" autocomplete="off" fieldId="rating" minValue="1"  maxValue="5" fieldRequired="true" />
                </div>
            </div>
        </x-form>
    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-testimonial" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

    <script>
        $(".select-picker").selectpicker();

        $('#save-testimonial').click(function (event) {
            $.easyAjax({
                url: "{{ route('superadmin.front-settings.testimonial-settings.store') }}",
                container: '#createMethods',
                type: "POST",
                redirect: true,
                disableButton: true,
                blockUI: true,
                data: $('#createMethods').serialize(),
                success: function(response) {
                    if (response.status == "success") {
                        window.location.reload();
                    }
                }
            })
        });
    </script>
