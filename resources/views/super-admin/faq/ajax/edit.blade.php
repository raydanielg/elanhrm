<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">
<div class="row">
    <div class="col-sm-12">
        <x-form id="save-faq-data-form" method="PUT">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-3 f-21 font-weight-normal border-bottom-grey">
                    @lang('app.edit') {{ $faq->title }}</h4>
                <div class="row px-3">
                    <div class="col-lg-6 col-md-6">
                        <x-forms.text :fieldLabel="__('app.title')" fieldName="title" fieldRequired="true" fieldId="title" :fieldValue="$faq->title" />
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <x-forms.label class="mt-3" fieldId="category"
                            :fieldLabel="__('app.category')" fieldRequired="true" >
                        </x-forms.label>
                        <x-forms.input-group>
                            <select class="form-control select-picker" name="category_id" id="category_id"
                                data-live-search="true">
                                <option value="">--</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @if ($faq->faq_category_id == $category->id) selected @endif>{{ $category->name }}</option>
                                @endforeach
                            </select>

                            <x-slot name="append">
                                <button id="addCategory" type="button"
                                    class="btn btn-outline-secondary border-grey">@lang('app.add')</button>
                            </x-slot>
                        </x-forms.input-group>
                    </div>

                    <div class="col-md-12 col-lg-12">
                        <div class="form-group my-3">
                            <x-forms.label class="my-3" fieldId="description"
                                :fieldLabel="__('app.description')">
                            </x-forms.label>
                            <div id="description">{!! $faq->description !!}</div>
                            <textarea name="description" id="description-text"
                                class="d-none">{!! $faq->description !!}</textarea>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <ul class="list-group" id="files-list">
                            @forelse($faq->files as $file)
                                <li class="list-group-item" id="task-file-{{  $file->id }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            {{ $file->filename }}
                                        </div>
                                        <div class="col-md-3">
                                            <span class="">{{ $file->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <a target="_blank" href="{{ $file->file_url }}"
                                               data-toggle="tooltip" data-original-title="@lang('app.view')"
                                               class="py-1 px-2 rounded-circle btn btn-secondary"><i
                                                        class="fa fa-search"></i></a>
                                            @if(is_null($file->external_link))
                                                <a href="{{ route('superadmin.faqs.download', $file->id) }}"
                                                   data-toggle="tooltip" data-original-title="@lang('app.download')"
                                                   class="py-1 px-2 rounded-circle btn btn-primary"><i
                                                            class="fa fa-download"></i></a>
                                            @endif

                                            <a href="javascript:;" data-toggle="tooltip" data-original-title="@lang('app.delete')" data-file-id="{{ $file->id }}"
                                               data-pk="list" class="py-1 rounded-circle btn btn-danger file-delete"><i class="fa fa-times"></i></a>

                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-md-10">
                                            @lang('messages.noFileUploaded')
                                        </div>
                                    </div>
                                </li>
                            @endforelse
                        </ul>
                        <x-forms.file-multiple class="mr-0 mr-lg-2 mr-md-2"
                            :fieldLabel="__('app.add') . ' ' .__('app.file')" fieldName="file"
                            fieldId="file-upload-dropzone" />
                        <input type="hidden" name="faqID" id="faqID">
                    </div>

                </div>


                <x-form-actions>
                    <x-forms.button-primary class="mr-3" id="save-faq-form" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('superadmin.faqs.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>

            </div>
        </x-form>

    </div>
</div>

<script src="{{ asset('vendor/jquery/dropzone.min.js') }}"></script>
<script>

    $(document).ready(function() {

        $('#addCategory').click(function() {
            const url = "{{ route('superadmin.faqCategory.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $(".select-picker").selectpicker();
        quillImageLoad('#description');


        Dropzone.autoDiscover = false;
            //Dropzone class
            myDropzone = new Dropzone("div#file-upload-dropzone", {
                dictDefaultMessage: "{{ __('app.dragDrop') }}",
                url: "{{ route('superadmin.faqs.file-store') }}",
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
                init: function() {
                    myDropzone = this;
                }
            });
            myDropzone.on('sending', function(file, xhr, formData) {
                var faq_id = $('#faqID').val();
                formData.append('faq_id', faq_id);
            });
            myDropzone.on('uploadprogress', function() {
                $.easyBlockUI();
            });
            myDropzone.on('completemultiple', function() {
                var msgs = "@lang('modules.projects.projectUpdated')";
                var redirect_url = $('#redirect_url').val();
                if (redirect_url != '') {
                    window.location.href = decodeURIComponent(redirect_url);
                }
                window.location.href = "{{ route('superadmin.faqs.index') }}"
            });

        $('#save-faq-form').click(function () {
            var note = document.getElementById('description').children[0].innerHTML;
            document.getElementById('description-text').value = note;

            $.easyAjax({
                url: "{{ route('superadmin.faqs.update', $faq->id) }}",
                container: '#save-faq-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-faq-form",
                data: $('#save-faq-data-form').serialize(),
                success: function(response) {
                    if (myDropzone.getQueuedFiles().length > 0) {
                        $('#faqID').val(response.faqID);
                        myDropzone.processQueue();
                    } else if (typeof response.redirectUrl !== 'undefined') {
                        window.location.href = response.redirectUrl;
                    } else {
                        window.location.href = "{{ route('superadmin.faqs.index') }}";
                    }
                }
            });
        });


    $('body').on('click', '.file-delete', function () {
        var id = $(this).data('file-id');
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

                var url = "{{ route('superadmin.faqs.file-destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token},
                    success: function (response) {
                        if (response.status == "success") {
                            $('#task-file-'+id).remove();
                        }
                    }
                });
            }
        });
    });

        init(RIGHT_MODAL);
    });

    $('#close-settings').click(function() {
        closeTaskDetail()
    });
</script>
