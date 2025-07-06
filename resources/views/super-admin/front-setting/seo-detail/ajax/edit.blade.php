<!-- FRONT SEO DETAIL START -->
<div class="modal-header">
    <h5 class="modal-title">@lang('app.update') @lang('superadmin.menu.seoDetails') ( {{$seoDetail->page_name}}
        ) {!! $lang->label !!}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>

<div class="modal-body">
    <div class="portlet-body">
        <x-form id="updateSeoDetail" method="PUT" class="ajax-form">
            <div class="form-group">
                <div class="row">
                    <div class="col-lg-12">
                        <x-forms.text :fieldLabel="__('superadmin.frontCms.seo_title')" fieldName="seo_title"
                                      autocomplete="off" fieldId="seo_title" :fieldValue="$seoDetail->seo_title"/>
                    </div>
                    <div class="col-lg-12">
                        <x-forms.text :fieldLabel="__('superadmin.frontCms.seo_author')" fieldName="seo_author"
                                      autocomplete="off" fieldId="seo_author" :fieldValue="$seoDetail->seo_author"/>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2"
                                              :fieldLabel="__('superadmin.frontCms.seo_description')"
                                              fieldName="seo_description"
                                              fieldId="seo_description" :fieldValue="$seoDetail->seo_description">
                            </x-forms.textarea>
                        </div>
                    </div>

                    <div class="col-lg-12 content_desc">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2"
                                              :fieldLabel="__('superadmin.frontCms.seo_keywords')"
                                              fieldName="seo_keywords"
                                              fieldId="seo_keywords" :fieldValue="$seoDetail->seo_keywords">
                            </x-forms.textarea>
                        </div>
                    </div>
                    <div class="col-lg-12 content_desc">
                        <x-forms.file :fieldLabel="__('superadmin.ogImage')" fieldName="og_image"
                                      fieldRequired="true" fieldId="og_image"
                                      :fieldValue="($seoDetail->og_image ? $seoDetail->masked_og_image_url : $seoDetail->og_image_url)"
                                      allowedFileExtensions="png jpg jpeg svg" />
                    </div>
                </div>
            </div>
        </x-form>
    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="mr-3 border-0">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="update-seo-detail" icon="check">@lang('app.save')</x-forms.button-primary>
</div>
<script>
    init('#updateSeoDetail');
    $('#update-seo-detail').click(function () {
        $.easyAjax({
            url: "{{ route('superadmin.front-settings.seo-detail.update', $seoDetail->id) }}",
            container: '#updateSeoDetail',
            type: "POST",
            file: true,
            redirect: true,
            disableButton: true,
            blockUI: true,
            data: $('#updateSeoDetail').serialize()
        })
    });
</script>

