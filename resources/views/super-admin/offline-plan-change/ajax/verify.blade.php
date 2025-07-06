<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('superadmin.menu.offlineRequest') ({{$pageTitle}})</h5>
    <button type="button"  class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <x-form id="request-data-form">
        <input type="hidden" name="status" value="verified">
        <input type="hidden" name="id" value="{{$offlinePlanChange->id}}">

        <div class="row">
            <div class="col-md-6">
                <x-forms.datepicker fieldId="pay_date" fieldRequired="true"
                    :fieldLabel="__('superadmin.paymentDate')" fieldName="pay_date"
                    :fieldValue="$offlinePlanChange->pay_date->timezone(global_setting()->timezone)->translatedFormat(global_setting()->date_format)"
                    :fieldPlaceholder="__('placeholders.date')" />
            </div>
            @if ($offlinePlanChange->package->package != 'lifetime')

                <div class="col-md-6">
                    <x-forms.datepicker fieldId="next_pay_date" fieldRequired="true"
                        :fieldLabel="__('superadmin.nextPaymentDate')" fieldName="next_pay_date"
                        :fieldValue="$offlinePlanChange->next_pay_date->timezone(global_setting()->timezone)->translatedFormat(global_setting()->date_format)"
                        :fieldPlaceholder="__('placeholders.date')" />
                </div>
            @endif


        </div>
    </x-form>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-request" icon="check">@lang('superadmin.offlineRequestStatusButton.verified')</x-forms.button-primary>
</div>

<script>
    $(document).ready(function() {

        const dp3 = datepicker('#pay_date', {
            position: 'bl',
            dateSelected: new Date("{{ str_replace('-', '/', $offlinePlanChange->pay_date) }}"),
            onSelect: (instance, date) => {
            if (typeof dp4 !== 'undefined') {
                if (typeof dp4.dateSelected !== 'undefined' && dp4.dateSelected.getTime() < date.getTime()) {
                dp4.setDate(date, true);
                }
                if (typeof dp4.dateSelected === 'undefined') {
                dp4.setDate(date, true);
                }
                dp4.setMin(date);
            }
            },
            ...datepickerConfig
        });
        @if ($offlinePlanChange->package->package != 'lifetime')

        const dp4 = datepicker('#next_pay_date', {
            position: 'bl',
            dateSelected: new Date("{{ str_replace('-', '/', $offlinePlanChange->next_pay_date) }}"),
            onSelect: (instance, date) => {
                dp3.setMax(date);
            },
            ...datepickerConfig
        });
        @endif

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
