@if ($global->front_design != 0)
    <div class="col-xl-12 col-lg-12 col-md-12 ntfcn-tab-content-left w-100 py-20">
        <div class="row">
            <div class="col-md-12">
                <x-forms.text :fieldLabel="__('superadmin.footer.footerCopyrightText').$lang->label"
                              fieldName="footer_copyright_text"
                              :fieldValue="$trFrontDetail ? $trFrontDetail->footer_copyright_text : ''"
                              autocomplete="off" fieldId="footer_copyright_text"/>
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
@endif
<div class="col-xl-12 col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-0">
    <input type="hidden" id="language" name="language_setting_id" value="{{ $lang->id }}">
    <div class="s-b-n-header mb-2">
        <h2 class="f-21 font-weight-normal text-capitalize border-bottom-grey mb-0 py-2 pl-20">
            @lang($pageTitle) {!!  $lang->label !!}</h2>
    </div>



    <div class="row pt-2">
        <div class="col-md-12 mb-2 ml-3">
            <button id="add-footer" type="button"
                    class="btn-primary rounded f-14 p-2"><i
                    class="fa fa-plus mr-1"></i>@lang('superadmin.footer.addFooter')</button>
        </div>
    </div>

    <div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-0">

        @include('super-admin.front-setting.footer-setting.footer-data')

    </div>

</div>
<script src="{{ asset('vendor/jquery/clipboard.min.js') }}"></script>
<script>
    /* open add footer modal */
    $('#add-footer').click(function () {
        var lang = $('#language').val();
        var url = "{{ route('superadmin.front-settings.footer-settings.create') }}?lang=" + lang;
        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });

    var clipboard = new ClipboardJS('.btn-copy-cron');

    clipboard.on('success', function (e) {
        Swal.fire({
            icon: 'success',
            text: "{{ __('app.copied') }}",
            toast: true,
            position: 'top-end',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            customClass: {
                confirmButton: 'btn btn-primary',
            },
            showClass: {
                popup: 'swal2-noanimation',
                backdrop: 'swal2-noanimation'
            },
        })
    });


    $('body').on('click', '.delete-table-row', function () {
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
                var url = "{{ route('superadmin.front-settings.footer-settings.destroy', ':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    blockUI: true,
                    data: {
                        'current_language_id': lang,
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function (response) {
                        if (response.status == "success") {
                            $('#example').html(response.html);
                            // $('.row'+id).fadeOut();
                        }
                    }
                });
            }
        });
    });
</script>
