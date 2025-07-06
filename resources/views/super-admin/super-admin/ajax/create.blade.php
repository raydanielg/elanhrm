@php
    $changeSuperadminRolePermission = user()->permission('change_superadmin_role');
@endphp
<div class="row">
    <div class="col-sm-12">
        <x-form id="save-superadmin-data-form">
            @include('sections.password-autocomplete-hide')

            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-3 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('superadmin.superadmin.create')</h4>

                <div class="row px-3">

                    @include('common.smtp-error')

                    <div class="col-lg-9 col-xl-10 ">
                        <div class="row">
                            <div class="col-md-12">

                                <div id="alert"></div>
                            </div>
                            <div class="col-md-4 ">
                                <x-forms.text fieldId="name" :fieldLabel="__('app.name')" fieldName="name"
                                              fieldRequired="true"
                                              :fieldPlaceholder="__('placeholders.name')"></x-forms.text>
                            </div>
                            <div class="col-md-5">
                                <x-forms.email fieldId="email"
                                               :fieldLabel="__('app.email').' ( '.__('messages.loginDetailsEmailed').')'"
                                               fieldName="email" :fieldPlaceholder="__('placeholders.email')"
                                               fieldRequired="true">
                                </x-forms.email>
                            </div>
                            @if ($changeSuperadminRolePermission == 'all' )
                                <div class="col-md-3">
                                    <x-forms.select fieldId="role" :fieldLabel="__('app.role')" fieldName="role">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                        @endforeach
                                    </x-forms.select>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-3 col-xl-2">
                        <x-forms.file allowedFileExtensions="png jpg jpeg svg" class="mr-0 mr-lg-2 mr-md-2 cropper"
                                      :fieldLabel="__('modules.profile.uploadPicture')" fieldName="image"
                                      fieldId="image"
                                      fieldHeight="119"/>
                    </div>

                </div>

                <x-form-actions>
                    <x-forms.button-primary class="mr-3" id="save-superadmin-form" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('superadmin.superadmin.index')"
                                           class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>

            </div>
        </x-form>

    </div>
</div>


<script>

    $(document).ready(function () {

        $('#save-superadmin-form').click(function () {
            $.easyAjax({
                url: "{{ route('superadmin.superadmin.store') }}",
                container: '#save-superadmin-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                file: true,
                buttonSelector: "#save-superadmin-form",
                data: $('#save-superadmin-data-form').serialize(),

            });
        });


        $('.cropper').on('dropify.fileReady', function (e) {
            const inputId = $(this).find('input').attr('id');
            let url = "{{ route('cropper', ':element') }}";
            url = url.replace(':element', inputId);
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        init(RIGHT_MODAL);
    });


</script>
