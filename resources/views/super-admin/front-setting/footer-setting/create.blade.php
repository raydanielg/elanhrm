<div class="modal-header">
    <h5 class="modal-title">@lang('superadmin.footer.addFooterMenu') ( {{$langCode->language_name}} <span class='flag-icon flag-icon-{{ $langCode->flag_code == 'en' ? 'gb' : strtolower($langCode->flag_code) }} flag-icon-squared'></span> )</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>

<div class="modal-body">
    <div class="portlet-body">
        <x-form id="createFooter" method="POST" class="ajax-form">
            <div class="form-group">
                <div class="row">
                    <input type="hidden" name="current_language_id" value="{{$lang}}">
                    {{-- <div class="col-lg-6">
                        <x-forms.select fieldId="language" :fieldLabel="__('superadmin.frontCms.defaultLanguage')"
                                        fieldName="language">
                            @foreach ($languageSettings as $language)
                                <option
                                    data-content="<span class='flag-icon flag-icon-{{ $language->language_code == 'en' ? 'gb' : strtolower($language->language_code) }} flag-icon-squared'></span> {{ $language->language_name }}"
                                    value="{{ $language->id }}">{{ $language->language_name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div> --}}
                    <div class="col-lg-6">
                        <x-forms.text :fieldLabel="__('app.title')" fieldName="title" autocomplete="off"
                                      fieldId="title" fieldRequired="true"/>
                    </div>
                    <div class="col-lg-6">
                        <x-forms.text :fieldLabel="__('superadmin.slug')" fieldName="slug" autocomplete="off"
                                      fieldId="slug" fieldRequired="true"/>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group my-3">
                            <x-forms.label fieldId="content_type_desc"
                                           :fieldLabel="__('superadmin.footerSettings.private')" :popover="__('superadmin.footerSettingPageType')">

                            </x-forms.label>
                            <div class="d-flex">
                                <x-forms.radio fieldId="private"
                                               :fieldLabel="__('app.yes')"
                                               fieldName="private"
                                               fieldValue="yes" class="page_type" checked="true">
                                </x-forms.radio>
                                <x-forms.radio fieldId="private"
                                               :fieldLabel="__('app.no')"
                                               fieldValue="no" :checked="true"
                                               fieldName="private" class="page_type"></x-forms.radio>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 publicType">
                        <div class="form-group my-3">
                            <x-forms.label fieldId="content_type_desc" :popover="__('superadmin.externalLink')"
                                           :fieldLabel="__('superadmin.footerSettings.pageContent')">
                            </x-forms.label>
                            <div class="d-flex">
                                <x-forms.radio fieldId="content_type_desc"
                                               :fieldLabel="__('superadmin.footerSettings.useDescription')"
                                               fieldName="content"
                                               fieldValue="desc" class="content_type" checked="true">
                                </x-forms.radio>
                                <x-forms.radio fieldId="content_type_link"
                                               :fieldLabel="__('superadmin.footerSettings.useExternalLink')"
                                               fieldValue="link"
                                               fieldName="content" class="content_type"></x-forms.radio>
                                               {{-- <i class="fa fa-question-circle" data-toggle="popover" data-placement="top" data-content="{{ __('superadmin.externalLink') }}" data-html="true" data-trigger="hover"></i> --}}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 content_desc">
                        <div class="form-group my-3">
                            <x-forms.label fieldId="description" :fieldLabel="__('app.description')">
                            </x-forms.label>
                            <div id="description"></div>
                            <textarea name="description" id="description_text" class="d-none"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-12 content_link d-none">
                        <x-forms.text :fieldLabel="__('superadmin.footerSettings.externalLink')"
                                      fieldName="external_link" autocomplete="off" fieldId="external_link"/>
                    </div>
                    <div class="col-lg-6">
                        <x-forms.select fieldId="status" :fieldLabel="__('app.status')" fieldName="status">
                            <option value="active">@lang('app.active')</option>
                            <option value="inactive">@lang('app.inactive')</option>
                        </x-forms.select>
                    </div>

                    <div class="col-lg-6 publicType">
                        <x-forms.select fieldId="type" :fieldLabel="__('superadmin.changeToPosition')" :popover="__('superadmin.footerPagePosition')" fieldName="type">
                            <option value="footer">@lang('superadmin.footer.footer')</option>
                            <option value="header">@lang('superadmin.header')</option>
                            <option value="both">@lang('superadmin.headerFooterBoth')</option>
                        </x-forms.select>
                    </div>
                </div>
                <div class="row mt-4">
                    <h4 class="col-12 mb-0 mt-2 pt-2 f-21 font-weight-normal text-capitalize border-top-grey content_desc">
                        @lang('superadmin.frontCms.seoDetails')</h4>
                    <div class="col-lg-12 content_desc">
                        <x-forms.text :fieldLabel="__('superadmin.frontCms.seo_title')" fieldName="seo_title"
                                      autocomplete="off" fieldId="seo_title"/>
                    </div>
                    <div class="col-lg-12 content_desc">
                        <x-forms.text :fieldLabel="__('superadmin.frontCms.seo_author')" fieldName="seo_author"
                                      autocomplete="off" fieldId="seo_author"/>
                    </div>
                    <div class="col-lg-12 content_desc">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2"
                                              :fieldLabel="__('superadmin.frontCms.seo_description')"
                                              fieldName="seo_description"
                                              fieldId="seo_description">
                            </x-forms.textarea>
                        </div>
                    </div>
                    <div class="col-lg-12 content_desc">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2"
                                              :fieldLabel="__('superadmin.frontCms.seo_keywords')"
                                              fieldName="seo_keywords"
                                              fieldId="seo_keywords">
                            </x-forms.textarea>
                        </div>
                    </div>
                </div>
            </div>
        </x-form>
    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-footer" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>
    $(".select-picker").selectpicker();

    $(document).ready(function () {
        quillImageLoad('#description');

    });

    $("body").on("click", ".content_type", function () {
        var contentType = $("input[name=content]:checked").val();
        if (contentType == 'link') {
            $(".content_link").removeClass('d-none');
            $(".content_desc").addClass('d-none');
        } else {
            $(".content_desc").removeClass('d-none');
            $(".content_link").addClass('d-none');
        }
    });

    $("body").on("click", ".page_type", function () {
        var contentType = $("input[name=private]:checked").val();
        $("#content_type_desc").prop("checked", true);
        if (contentType == 'no') {
            // $(".publicType").removeClass('d-none');
            $(".publicType").removeClass('d-none');
            $(".content_desc").removeClass('d-none');
            $(".content_link").addClass('d-none');
        } else {
            $(".publicType").addClass('d-none');
        }
    });

    $("#save-footer").click(function (event) {
        document.getElementById('description_text').value = document.getElementById('description').children[0]
            .innerHTML;

        $.easyAjax({
            url: "{{ route('superadmin.front-settings.footer-settings.store') }}",
            container: '#createFooter',
            type: "POST",
            blockUI: true,
            data: $('#createFooter').serialize(),
            success: function(response) {
                if (response.status == "success") {
                    $('#example').html(response.html);
                    $(MODAL_LG).modal('hide');
                }
            }
        })
    });


    $('#title').on('change', function () {
        var title = $(this).val();
        var csrf = "{{ csrf_token() }}";
        $.easyAjax({
            url: "{{ route('superadmin.front-settings.footer-settings.generate_slug') }}",
            type: "POST",
            data: {
                title: title,
                _token: csrf
            },
            success: function (response) {
                $('#slug').val(response.slug);
            }
        })
    });
    init('#createFooter');
</script>
