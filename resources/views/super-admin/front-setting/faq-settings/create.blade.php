<div class="modal-header">
    <h5 class="modal-title">@lang('app.addNew') @lang('superadmin.menu.faq') ( {{$langCode->language_name}} <span class='flag-icon flag-icon-{{ $langCode->flag_code == 'en' ? 'gb' : strtolower($langCode->flag_code) }} flag-icon-squared'></span> )</h5>

    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>

<div class="modal-body">
    <div class="portlet-body">
        <x-form id="createFAQ" method="POST" class="ajax-form">
            <div class="form-group">
                <div class="row">
                    <input type="hidden" name="current_language_id" value="{{$lang}}">
                    <div class="col-lg-6">
                        <x-forms.text :fieldLabel="__('superadmin.question')" fieldName="question" autocomplete="off" fieldId="question" fieldRequired="true" />
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group my-3">
                            <x-forms.label fieldId="answer" :fieldLabel="__('superadmin.answer')">
                            </x-forms.label>
                            <div id="answer"></div>
                            <textarea name="answer" id="answer_text" class="d-none"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </x-form>
    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-faq" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>
    $(".select-picker").selectpicker();

    $(document).ready(function () {
        quillImageLoad('#answer');
    });


    $('#save-faq').click(function() {
        document.getElementById('answer_text').value = document.getElementById('answer').children[0].innerHTML;

        $.easyAjax({
            url: "{{ route('superadmin.front-settings.faq-settings.store') }}",
            container: '#createFAQ',
            type: "POST",
            blockUI: true,
            data: $('#createFAQ').serialize(),
            success: function(response) {
                if (response.status == "success") {
                    $('#example').html(response.html);
                    $(MODAL_LG).modal('hide');
                }
            }
        })
    });

    init('#createFAQ');
</script>
