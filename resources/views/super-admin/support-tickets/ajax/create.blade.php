<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">

<div class="row">
    <div class="col-sm-12">
        <x-form id="save-ticket-data-form">
            <input type="hidden" id="replyID">
            <div class="bg-white rounded add-client">
                <h4 class="p-20 mb-0 f-21 font-weight-normal border-bottom-grey">
                    @lang('modules.tickets.ticketDetail')</h4>
                <div class="p-20 row">
                    @if (user()->is_superadmin)
                    <div class="col-md-6">
                        <x-forms.select2-ajax fieldRequired="true" fieldId="requested_for" fieldName="requested_for"
                                              format="true"
                                              :fieldLabel="__('superadmin.requestedForCompany')"
                                              :route="route('superadmin.get.company-ajax')"
                                              :placeholder="__('placeholders.searchForCompany')"
                        ></x-forms.select2-ajax>
                    </div>
                    @else
                        <input type="hidden" name="requested_for" value="{{ user()->company_id }}">
                    @endif

                    <div @class([
                        "col-md-6"=> user()->is_superadmin,
                        "col-md-12"=> !user()->is_superadmin,
                        ])>
                        <x-forms.text :fieldLabel="__('modules.tickets.ticketSubject')" fieldName="subject"
                                      fieldRequired="true" fieldId="subject"/>
                    </div>

                    <div class="col-md-12">
                        <div class="my-3 form-group">
                            <x-forms.label fieldId="description" :fieldLabel="__('app.description')"
                                           fieldRequired="true">
                            </x-forms.label>
                            <div id="description"></div>
                            <textarea name="description" id="description-text" class="d-none"></textarea>
                        </div>
                        <div class="my-3">
                            <a class="f-15 f-w-500" href="javascript:;" id="add-file"><i
                                    class="mr-1 fa fa-paperclip font-weight-bold"></i>@lang('modules.projects.uploadFile')
                            </a>
                        </div>
                    </div>
                </div>

                <div class="px-4 row">
                    <div class="col-md-12">
                        <x-forms.file-multiple class="mr-0 mr-lg-2 mr-md-2 upload-section d-none"
                                               :fieldLabel="__('app.add') . ' ' .__('app.file')" fieldName="file"
                                               fieldId="task-file-upload-dropzone"/>
                        <input type="hidden" name="image_url" id="image_url">
                    </div>

                </div>

                @if (user()->is_superadmin)
                <h4 class="p-20 mb-0 f-21 font-weight-normal border-top-grey">
                    <a href="javascript:;" class="text-dark toggle-other-details"><i class="fa fa-chevron-down"></i>
                        @lang('modules.client.clientOtherDetails')</a>
                </h4>

                <div class="p-20 row d-none" id="other-details">

                    <div class="col-md-6 col-lg-4">
                        <x-forms.select fieldId="agent_id" :fieldLabel="__('modules.tickets.agent')"
                                        fieldName="agent_id">
                            @foreach ($superadmins as $agent)
                                <option
                                    data-content="<div class='mr-1 d-inline-block'><img class='taskEmployeeImg rounded-circle' src='{{ $agent->image_url }}' ></div> {{ $agent->name }}"
                                    value="{{ $agent->id }}">
                                    {{ $agent->name . ' [' . $agent->email . ']' }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>


                    <div class="col-md-6 col-lg-4">
                        <x-forms.select fieldId="priority" :fieldLabel="__('modules.tasks.priority')"
                                        fieldName="priority">
                            <option value="low">@lang('app.low')</option>
                            <option value="medium">@lang('app.medium')</option>
                            <option value="high">@lang('app.high')</option>
                            <option value="urgent">@lang('app.urgent')</option>
                        </x-forms.select>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <x-forms.label class="mt-3" fieldId="ticket_type_id"
                                       :fieldLabel="__('modules.invoices.type')">
                        </x-forms.label>
                        <x-forms.input-group>
                            <select class="form-control select-picker" name="type_id" id="ticket_type_id"
                                    data-live-search="true" data-size="8">
                                <option value="">--</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->type }}</option>
                                @endforeach
                            </select>
                            <x-slot name="append">
                                <button id="add-type" type="button"
                                        class="btn btn-outline-secondary border-grey">@lang('app.add')</button>
                            </x-slot>
                        </x-forms.input-group>
                    </div>
                </div>
                @endif

                <x-form-actions>
                    <x-forms.button-primary id="save-ticket-form" class="mr-3" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('superadmin.support-tickets.index')"
                                           class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>

            </div>
        </x-form>

    </div>
</div>


<script src="{{ asset('vendor/jquery/dropzone.min.js') }}"></script>
<script src="{{asset('/vendor/jquery/select2.min.js')}}"></script>

<script>

    $(document).ready(function () {

        $('#add-file').click(function () {
            $('.upload-section').removeClass('d-none');
            $('#add-file').addClass('d-none');
            window.scrollTo(0, document.body.scrollHeight);
        });

        Dropzone.autoDiscover = false;
        //Dropzone class
        taskDropzone = new Dropzone("div#task-file-upload-dropzone", {
            dictDefaultMessage: "{{ __('app.dragDrop') }}",
            url: "{{ route('superadmin.support-ticket-files.store') }}",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            paramName: "file",
            maxFilesize: DROPZONE_MAX_FILESIZE,
            maxFiles: 10,
            autoProcessQueue: false,
            uploadMultiple: true,
            addRemoveLinks: true,
            parallelUploads: 10,
            acceptedFiles: DROPZONE_FILE_ALLOW,
            init: function () {
                taskDropzone = this;
            }
        });

        taskDropzone.on('sending', function (file, xhr, formData) {
            var ids = $('#replyID').val();
            formData.append('ticket_reply_id', ids);
            $.easyBlockUI();
        });
        taskDropzone.on('uploadprogress', function () {
            $.easyBlockUI();
        });
        taskDropzone.on('completemultiple', function () {
            var msgs = "@lang('messages.addDiscussion')";
            window.location.href = "{{ route('superadmin.support-tickets.index') }}";
        });

        quillImageLoad('#description');

        /* open add agent modal */
        $('body').on('click', '#add-type', function () {
            var url = "{{ route('superadmin.support-ticketTypes.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('#save-ticket-form').click(function () {
            var note = document.getElementById('description').children[0].innerHTML;
            document.getElementById('description-text').value = note;

            const url = "{{ route('superadmin.support-tickets.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-ticket-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-ticket-form",
                data: $('#save-ticket-data-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        if (taskDropzone.getQueuedFiles().length > 0) {
                            $('#replyID').val(response.replyID);
                            taskDropzone.processQueue();
                        } else {
                            window.location.href = response.redirectUrl;
                        }
                    }

                }
            });
        });

        $('.toggle-other-details').click(function () {
            $(this).find('svg').toggleClass('fa-chevron-down fa-chevron-up');
            $('#other-details').toggleClass('d-none');
        });

        init(RIGHT_MODAL);
    });
</script>
