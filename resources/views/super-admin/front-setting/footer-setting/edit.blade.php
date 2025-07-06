<div class="modal-header">
    <h5 class="modal-title">@lang('superadmin.footer.editFooterMenu') ( {{$langCode->language_name}} <span class='flag-icon flag-icon-{{ $langCode->flag_code == 'en' ? 'gb' : strtolower($langCode->flag_code) }} flag-icon-squared'></span> )</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>

<div class="modal-body">
    <div class="portlet-body">
        <x-form id="editFooter" method="PUT" class="ajax-form">
            <div class="form-group">
                <div class="row">
                    <input type="hidden" name="current_language_id" value="{{$lang}}">
                    {{-- <div class="col-lg-6">
                        <x-forms.select fieldId="language" :fieldLabel="__('superadmin.frontCms.defaultLanguage')"
                                        fieldName="language">
                            @foreach ($languageSettings as $language)
                                <option
                                    @if($language->id === $footer->language_setting_id) selected @endif
                                data-content="<span class='flag-icon flag-icon-{{ $language->language_code == 'en' ? 'gb' : strtolower($language->language_code) }} flag-icon-squared'></span> {{ $language->language_name }}"
                                    value="{{ $language->id }}">{{ $language->language_name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div> --}}
                    <div class="col-lg-6">
                        <x-forms.text :fieldLabel="__('app.title')" fieldName="title" autocomplete="off" fieldId="title"
                                      :fieldValue="$footer->name"/>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group my-3">
                            <x-forms.label fieldId="private" :popover="__('superadmin.footerSettingPageType')"
                                           :fieldLabel="__('superadmin.footerSettings.private')">
                            </x-forms.label>
                            <div class="d-flex">
                                <x-forms.radio fieldId="page_type_private"
                                               :fieldLabel="__('app.yes')"
                                               fieldName="private"
                                               :checked="($footer->private == 1)"
                                               fieldValue="yes" class="page_type">
                                </x-forms.radio>
                                <x-forms.radio fieldId="private"
                                               :fieldLabel="__('app.no')"
                                               fieldValue="no"
                                               :checked="($footer->page_type == 0)"
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
                                               fieldValue="desc" class="content_type"
                                               :checked="(!is_null($footer->description))">
                                </x-forms.radio>
                                <x-forms.radio fieldId="content_type_link"
                                               :fieldLabel="__('superadmin.footerSettings.useExternalLink')"
                                               fieldValue="link"
                                               fieldName="content" class="content_type"
                                               :checked="(!is_null($footer->external_link))"></x-forms.radio>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 content_desc">
                        <div class="form-group my-3">
                            <x-forms.label fieldId="description" :fieldLabel="__('app.description')">
                            </x-forms.label>
                            <div id="description">{!! $footer->description !!}</div>
                            <textarea name="description" id="description_text" class="d-none"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-12 content_link">
                        <x-forms.text :fieldLabel="__('superadmin.footerSettings.externalLink')" fieldName="external_link"
                                      autocomplete="off" fieldId="external_link" :fieldValue="$footer->external_link"/>
                    </div>
                    <div class="col-lg-6">
                        <x-forms.select fieldId="status" :fieldLabel="__('app.status')" fieldName="status">
                            <option @if($footer->status == 'active') selected
                                    @endif value="active">@lang('app.active')</option>
                            <option @if($footer->status == 'inactive') selected
                                    @endif value="inactive">@lang('app.inactive')</option>
                        </x-forms.select>
                    </div>

                    <div class="col-lg-6 publicType">
                        <x-forms.select fieldId="type" :fieldLabel="__('superadmin.changeToPosition')" :popover="__('superadmin.footerPagePosition')" fieldName="type">
                            <option @if($footer->type == 'footer') selected
                                    @endif value="footer">@lang('superadmin.footer.footer')</option>
                            <option @if($footer->type == 'header') selected
                                    @endif value="header">@lang('superadmin.header')</option>
                            <option @if($footer->type == 'both') selected @endif value="both">@lang('superadmin.headerFooterBoth')</option>
                        </x-forms.select>
                    </div>
                </div>
                <div class="row mt-4">
                    <h4 class="col-12 mb-0 mt-2 pt-4 f-21 font-weight-normal text-capitalize border-top-grey content_desc">
                        @lang('superadmin.frontCms.seoDetails')</h4>
                    <div class="col-lg-12 content_desc">
                        <x-forms.text :fieldLabel="__('superadmin.frontCms.seo_title')" fieldName="seo_title"
                                      autocomplete="off" fieldId="seo_title"
                                      :fieldValue="$seoDetail ? $seoDetail->seo_title : ''"/>
                    </div>
                    <div class="col-lg-12 content_desc">
                        <x-forms.text :fieldLabel="__('superadmin.frontCms.seo_author')" fieldName="seo_author"
                                      autocomplete="off" fieldId="seo_author"
                                      :fieldValue="$seoDetail ? $seoDetail->seo_author : ''"/>
                    </div>
                    <div class="col-lg-12 content_desc">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2"
                                              :fieldLabel="__('superadmin.frontCms.seo_description')"
                                              fieldName="seo_description"
                                              fieldId="seo_description"
                                              :fieldValue="$seoDetail ? $seoDetail->seo_description : ''">
                            </x-forms.textarea>
                        </div>
                    </div>
                    <div class="col-lg-12 content_desc">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2"
                                              :fieldLabel="__('superadmin.frontCms.seo_keywords')"
                                              fieldName="seo_keywords"
                                              fieldId="seo_keywords"
                                              :fieldValue="$seoDetail ? $seoDetail->seo_keywords : ''">
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
    <x-forms.button-primary id="update-footer" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>

    @if($footer->private == 1)
        $("#page_type_private").prop("checked", true);
        $(".publicType").addClass('d-none');
    @else
        $("#page_type_public").prop("checked", true);
        $(".publicType").removeClass('d-none');
        $(".content_desc").removeClass('d-none');
        $(".content_link").addClass('d-none');
    @endif

    function changeContentType() {
        var contentType = $("input[name=content]:checked").val();
        if (contentType == 'link') {
            $(".content_link").removeClass('d-none');
            $(".content_desc").addClass('d-none');
        } else {
            $(".content_desc").removeClass('d-none');
            $(".content_link").addClass('d-none');
        }
    }

    $("body").on("click", ".page_type", function () {
        var contentType = $("input[name=private]:checked").val();
        $("#content_type_desc").prop("checked", true);

        if (contentType == 'no') {
            $(".publicType").removeClass('d-none');
            $(".content_desc").removeClass('d-none');
            $(".content_link").addClass('d-none');
        } else {
            $(".publicType").addClass('d-none');
        }
    });

    $("body").on("click", ".content_type", function () {
        changeContentType();
    });

    $(document).ready(function () {
        quillImageLoad('#description');
        changeContentType();
    });

    $("#update-footer").click(function (event) {
        document.getElementById('description_text').value = document.getElementById('description').children[0]
            .innerHTML;

        $.easyAjax({
            url: "{{ route('superadmin.front-settings.footer-settings.update', $footer->id) }}",
            container: '#editFooter',
            type: "POST",
            blockUI: true,
            data: $('#editFooter').serialize(),
            success: function(response) {
                if (response.status == "success") {
                    $('#example').html(response.html);
                    $(MODAL_LG).modal('hide');
                }
            }
        })
    });

    init('#editFooter');

 </script>
