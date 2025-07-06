@php
$manageFaqCategoryPermission = user()->permission('manage_faq_category');
@endphp
<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">
<div class="row">
    <div class="col-sm-12">
        <x-form id="save-faq-data-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-3 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('app.create') @lang('superadmin.menu.adminFaq')</h4>
                <div class="row px-3">
                    <div class="col-lg-6 col-md-6">
                        <x-forms.text :fieldLabel="__('app.title')" fieldName="title" fieldRequired="true" fieldId="title"/>
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
                                    <option value="{{ $category->id }}"  @selected($category->id == request()->id)>
                                        {{ $category->name }}</option>
                                @endforeach
                            </select>
                            @if($manageFaqCategoryPermission == 'all')
                                <x-slot name="append">
                                    <button id="addCategory" type="button"
                                        class="btn btn-outline-secondary border-grey">@lang('app.add')</button>
                                </x-slot>
                            @endif
                        </x-forms.input-group>
                    </div>

                    <div class="col-md-12 col-lg-12">
                        <div class="form-group my-3">
                            <x-forms.label class="my-3" fieldId="description"
                                :fieldLabel="__('app.description')">
                            </x-forms.label>
                            <div id="description"></div>
                            <textarea name="description" id="description-text"
                                class="d-none"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-12">
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
                dictRemoveFile: '@lang('app.removeFile')',
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
                url: "{{ route('superadmin.faqs.store') }}",
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

        init(RIGHT_MODAL);
    });

    $('#close-settings').click(function() {
        closeTaskDetail()
    });
</script>
