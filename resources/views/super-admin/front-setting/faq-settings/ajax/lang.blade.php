<div class="col-xl-12 col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-20">
    <input type="hidden" id="language" name="language_setting_id" value="{{ $lang->id }}">

    <div class="row">

        <div class="col-md-12">
            <x-forms.text :fieldLabel="__('app.title'). $lang->label" fieldName="title"
                          :fieldValue="$trFrontDetail ? $trFrontDetail->faq_title : ''" autocomplete="off"
                          fieldId="title"/>
        </div>
    </div>
</div>
<!-- Buttons Start -->
<div class="w-100 border-bottom-grey">
    <x-setting-form-actions>
        <div class="d-flex">
            <x-forms.button-primary class="mr-3 w-100" icon="check" id="saveFrontSetting">@lang('app.update')
            </x-forms.button-primary>
        </div>
    </x-setting-form-actions>
</div>

<div class="col-xl-12 col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-20">
    <div class="s-b-n-header mb-2">
        <h3 class="heading-h3 mb-3">
            @lang($pageTitle) <span class="f-14">{!!  $lang->label !!}</span></h3>
    </div>

    <div class="row m-l-3">
        <div class="col-md-12 mb-3">
            <x-forms.button-primary id="add-faq" icon="plus">@lang('app.addNew') @lang('superadmin.menu.faq')</x-forms.btn-primary>
        </div>
    </div>

    <div id="example">
        @include('super-admin.front-setting.faq-settings.faq-data')
    </div>
</div>
<script>
    /* open add faq modal */
    $('#add-faq').click(function () {
        var lang = $('#language').val();
        var url = "{{ route('superadmin.front-settings.faq-settings.create') }}?lang="+lang;
        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });

    $('body').on('click', '.delete-table-row', function() {
        var id = $(this).data('id');
        var lang = $('#language').val();

        Swal.fire({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.recoverRecord')",
            icon: 'warning',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: "@lang('messages.confirmDelete')",
            cancelButtonText: "@lang('app.cancel')",
            customClass: {
                confirmButton: 'btn btn-primary mr-3',
                cancelButton: 'btn btn-secondary'
            },
            showClass: {
                popup: 'swal2-noanimation',
                backdrop: 'swal2-noanimation'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                var url = "{{ route('superadmin.front-settings.faq-settings.destroy', ':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    blockUI: true,
                    data: {
                        'current_language_id' : lang,
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            $('#example').html(response.html);
                            // $('.row'+id).fadeOut();
                        }
                    }
                });
            }
        });
    });

        /* open add footer modal */
        $('body').on('click', '.edit-faq',function () {
        var id = $(this).data('id');
        var lang = $('#language').val();

        var url = "{{ route('superadmin.front-settings.faq-settings.edit', ':id')}}?lang="+lang;
            url = url.replace(':id', id);

        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });
</script>
