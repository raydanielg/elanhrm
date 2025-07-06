<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('app.client')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<x-form id="save-client-data-form">
    <input type="hidden" name="ajax_create" value="1">
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <x-forms.text fieldId="name" :fieldLabel="__('modules.client.clientName')" fieldName="name"
                    fieldRequired="true" :fieldPlaceholder="__('placeholders.name')"
                    :fieldValue="$lead->client_name_salutation ?? ''"></x-forms.text>
            </div>
            <div class="col-md-12">
                <x-forms.email fieldId="email" :fieldLabel="__('app.email')" fieldName="email"
                    :popover="__('messages.requiredForLogin')" :fieldPlaceholder="__('placeholders.email')"
                    :fieldValue="$lead->client_email ?? ''">
                </x-forms.email>
            </div>
            <div class="col-md-12">
                <x-forms.text class="mb-3" fieldId="company_name"
                    :fieldLabel="__('modules.client.companyName')" fieldName="company_name"
                    :fieldPlaceholder="__('placeholders.company')" :fieldValue="$lead->company_name ?? ''"></x-forms.text>
            </div>
            <div class="col-md-12">
                <div class="form-group my-3">
                    <label class="f-14 text-dark-grey mb-12 w-100"
                        for="usr">@lang('modules.client.clientCanLogin')</label>
                    <div class="d-flex">
                        <x-forms.radio fieldId="login-yes" :fieldLabel="__('app.yes')" fieldName="login"
                            fieldValue="enable">
                        </x-forms.radio>
                        <x-forms.radio fieldId="login-no" :fieldLabel="__('app.no')" fieldValue="disable" checked="true"
                            fieldName="login"></x-forms.radio>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
        <x-forms.button-primary id="save-category" icon="check">@lang('app.save')</x-forms.button-primary>
    </div>
</x-form>

<script>
    init(MODAL_DEFAULT);

    $(document).ready(function () {
        setTimeout(function () {
            $('[data-toggle="popover"]').popover();
        }, 500);
    });

    $('#random_password').click(function() {
        const randPassword = Math.random().toString(36).substr(2, 8);

        $('#password').val(randPassword);
    });

    $('#save-category').click(function() {
        var url = "{{ route('clients.store') }}";
        $.easyAjax({
            url: url,
            container: '#save-client-data-form',
            type: "POST",
            blockUI: true,
            data: $('#save-client-data-form').serialize(),
            success: function(response) {
                if (response.status == 'success') {
                    if ($('#client_list_id').length > 0) {
                        $('#client_list_id').html('<option value="">--</option>' +
                            response.teamData);
                        $('#client_list_id').selectpicker('refresh');

                        $('#client_billing_address').addClass('d-none');
                        $('#client_billing_address_editable').removeClass('d-none').val('');

                        $('#project_id').html(response.project);
                        $('#project_id').selectpicker('refresh');

                    }
                    $(MODAL_DEFAULT).modal('hide');
                }
            }
        })
    });

    $(function () {
        $('[data-toggle="popover"]').popover();
    });
</script>
