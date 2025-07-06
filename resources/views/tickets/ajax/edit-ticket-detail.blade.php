<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('modules.tickets.ticketDetail')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>

<div class="modal-body">
    <div class="portlet-body">
        <x-form id="editTicketDetail" method="PUT" class="ajax-form">
            <div class="form-body">
                <div class="row">
                    <div class="col-lg-12">
                        <input type="hidden" id="ticket_id" name="ticket_id" value="{{ $ticket->id }}">
                        <x-forms.text class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.tickets.ticketSubject')"
                            fieldRequired="true" fieldName="subject"
                            :fieldValue="$ticket->subject" fieldId="subject"/>

                        @if($reply)
                            <input type="hidden" id="ticket_reply_id" name="ticket_reply_id" value="{{ $reply->id }}">
                            @if(!empty($reply->message))
                                <x-forms.label fieldId="description" :fieldLabel="__('app.description')"
                                        fieldRequired="true">
                                </x-forms.label>
                                <div id="description3">{!! $reply->message !!}</div>
                                <textarea name="description" id="description-text3" class="d-none"></textarea>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </x-form>
    </div>
</div>

<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-ticket" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>

    $(document).ready(function() {
        if (document.querySelector('#description3')) {
            quillMention(null, '#description3');
        }
    });

    $('#save-ticket').click(function () {

        if (document.getElementById('description3')) {
            var note = document.getElementById('description3').children[0].innerHTML;
            document.getElementById('description-text3').value = note;
        }

        $.easyAjax({
            url: "{{route('tickets.update_detail', $ticket->id)}}",
            container: '#editTicketDetail',
            type: "POST",
            blockUI: true,
            data: $('#editTicketDetail').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }else{
                    $('#description3-text').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            }
        })
    });
</script>
