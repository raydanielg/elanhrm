<div class="modal-header">
    <h5 class="modal-title">@lang('app.addNew') @lang('superadmin.menu.frontClient') ( {{$langCode->language_name}} <span class='flag-icon flag-icon-{{ $langCode->flag_code == 'en' ? 'gb' : strtolower($langCode->flag_code) }} flag-icon-squared'></span> )</h5>

    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>

<div class="modal-body">
    <div class="portlet-body">
        <x-form id="editFrontClient" method="PUT" class="ajax-form">
            <div class="form-group">
                <div class="row">
                    <input type="hidden" name="current_language_id" value="{{$lang}}">
                    <div class="col-lg-6">
                        <x-forms.text :fieldLabel="__('app.title')" fieldName="title" autocomplete="off" fieldId="title" :fieldValue="$client->title"/>
                    </div>
                    <div class="col-lg-12">
                        <x-forms.file allowedFileExtensions="png jpg jpeg svg" class="mr-lg-2 mr-md-2 mr-0"
                            :fieldLabel="__('superadmin.types.image') . ' (400x352)'" :fieldValue="$client->image_url" fieldName="image" fieldId="image" :popover="__('messages.featureImageSizeMessage')">
                        </x-forms.file>
                    </div>
                </div>
            </div>
        </x-form>
    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-front-client" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>

    $(".select-picker").selectpicker();

    $('#save-front-client').click(function() {
        $.easyAjax({
            url: "{{ route('superadmin.front-settings.client-settings.update', $client->id) }}",
            container: '#editFrontClient',
            type: "POST",
            blockUI: true,
            file: true,
            success: function(response) {
                if (response.status == "success") {
                    $('#example').html(response.html);
                    $(MODAL_LG).modal('hide');
                }
            }
        })
    });

    init('#editFrontClient');

</script>
