<div class="col-xl-12 col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-20">
    <input type="hidden" id="language" name="language_setting_id" value="{{ $lang->id }}">

    <div class="row">
        <div class="col-md-12">
            <x-forms.text :fieldLabel="__('app.title'). $lang->label" fieldName="title"
                          :fieldValue="$trFrontDetail ? $trFrontDetail->client_title : ''" autocomplete="off"
                          fieldId="title"/>
        </div>
        <div class="col-md-12">
            <x-forms.textarea fieldId="detail" :fieldLabel="__('app.description'). $lang->label" fieldName="detail"
                              :fieldValue="$trFrontDetail ? $trFrontDetail->client_detail : ''">
            </x-forms.textarea>
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
            <button id="add-client" type="button"
                    class="btn-primary rounded f-14 p-2"><i
                    class="fa fa-plus mr-1"></i>@lang('app.addNew') @lang('superadmin.menu.frontClient')</button>
        </div>
    </div>

    <!-- LEAVE SETTING START -->
    <div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-0">

        <x-table class="table-sm-responsive table mb-0">
            <x-slot name="thead">
                <th>@lang('app.name')</th>
                <th>@lang('app.language')</th>
                <th>@lang('superadmin.types.image')</th>
                <th class="text-right">@lang('app.action')</th>
            </x-slot>

            @forelse($clients as $client)
                <tr class="row{{ $client->id }}">
                    <td>{{ $client->title }}</td>
                    <td>{{ $client->language ? $client->language->language_name : 'English' }}</td>
                    <td>
                        <img height="40" width="120" src="{{$client->image_url }}" alt=""/>
                    </td>
                    <td class="text-right">
                        {{-- <div class="task_view">
                            <a class="task_view_more d-flex align-items-center justify-content-center edit-client"
                               href="javascript:;" data-id="{{$client->id}}">
                                <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                            </a>
                        </div> --}}
                        <div class="task_view mt-1 mt-lg-0 mt-md-0">
                            <a class="task_view_more d-flex align-items-center justify-content-center delete-table-row"
                               href="javascript:;" data-id="{{ $client->id }}">
                                <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        <x-cards.no-record icon="list" :message="__('messages.noRecordFound')"/>
                    </td>
                </tr>
            @endforelse

        </x-table>

    </div>
</div>
<script>
        /* open add footer modal */
    $('#add-client').click(function () {
        var lang = $('#language').val();
        var url = "{{ route('superadmin.front-settings.client-settings.create') }}?lang="+lang;
        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });

    /* open add footer modal */
    $('body').on('click', '.edit-client', function () {
        var id = $(this).data('id');
        var lang = $('#language').val();

        var url = "{{ route('superadmin.front-settings.client-settings.edit', ':id')}}?lang="+lang;
            url = url.replace(':id', id);

        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
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
                var url = "{{ route('superadmin.front-settings.client-settings.destroy', ':id') }}";
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
                    success: function (response) {
                        if (response.status == "success") {
                            // $('.row'+id).fadeOut();
                            $('#example').html(response.html);
                        }
                    }
                });
            }
        });
    });
</script>
