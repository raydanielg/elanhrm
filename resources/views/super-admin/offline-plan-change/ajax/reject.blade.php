<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('superadmin.menu.offlineRequest') ({{$pageTitle}})</h5>
    <button type="button"  class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">

    <x-form id="request-data-form">
        <input type="hidden" name="status" value="rejected">
        <input type="hidden" name="id" value="{{$offlinePlanChange->id}}">
        <div class="row">
            <div class="col-md-12">
                <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2"
                                          :fieldLabel="__('app.remark')" fieldName="remark" :fieldRequired="true"
                                          fieldId="remark">
                        </x-forms.textarea>
            </div>
        </div>
    </x-form>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-request" icon="check">@lang('superadmin.offlineRequestStatusButton.rejected')</x-forms.button-primary>
</div>

<script>
    $(document).ready(function() {

        $('#save-request').click(function() {

            const url = "{{ route('superadmin.offline-plan.changePlan') }}";

            $.easyAjax({
                url: url,
                container: '#request-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-request",
                data: $('#request-data-form').serialize(),
                success: function(response) {
                    if (response.status == "success") {
                        window.location.reload();
                    }

                }
            });
        });

        init(MODAL_LG)
    });

</script>
