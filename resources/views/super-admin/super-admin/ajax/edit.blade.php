@php
    $changeSuperadminRolePermission = user()->permission('change_superadmin_role');
@endphp
<div class="row">
    <div class="col-sm-12">
        <x-form id="save-superadmin-data-form" method="PUT">
            @include('sections.password-autocomplete-hide')

            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-3 f-21 font-weight-normal  border-bottom-grey">
                    @lang('superadmin.superadmin.edit', ['name' => $superAdmin->name])</h4>

                <div class="row p-3">
                    @include('common.smtp-error')
                    <div class="col-lg-9 col-xl-10">
                        <div class="row">
                            <div class="col-md-3">
                                <x-forms.text fieldId="name" :fieldLabel="__('app.name')" fieldName="name"
                                              :fieldValue="$superAdmin->name"
                                              fieldRequired="true"
                                              :fieldPlaceholder="__('placeholders.name')"></x-forms.text>
                            </div>
                            <div class="col-md-3">
                                <x-forms.email fieldId="email" :fieldLabel="__('app.email')"
                                               :fieldValue="$superAdmin->email" fieldName="email"
                                               :fieldPlaceholder="__('placeholders.email')" fieldRequired="true">
                                </x-forms.email>
                            </div>

                            @if (
                            ((in_array('superadmin', $userRoles) && in_array('superadmin', user_roles()))
                            || (!in_array('superadmin', $userRoles)))
                            && $superAdmin->id != user()->id
                            && $changeSuperadminRolePermission == 'all'
                            )
                                <div class="col-md-3">
                                    <x-forms.select fieldId="role" :fieldLabel="__('app.role')" fieldName="role">
                                        @foreach ($roles as $role)
                                            <option
                                            @if (
                                                (in_array($role->name, $userRoles) && $role->name == 'superadmin')
                                                || (in_array($role->name, $userRoles) && !in_array('superadmin', $userRoles))
                                            )
                                            selected
                                            @endif
                                            value="{{ $role->id }}">{{ $role->display_name }}</option>
                                        @endforeach
                                    </x-forms.select>
                                </div>
                            @endif
                            @if($superAdmin->id !== user()->id)
                            <div class="col-md-3">
                                <x-forms.select fieldId="status" :fieldLabel="__('app.status')"
                                                fieldName="status">
                                    <option @if($superAdmin->status == 'active') selected
                                            @endif  value="active">@lang('app.active')</option>
                                    <option @if($superAdmin->status == 'deactive') selected
                                            @endif   value="deactive">@lang('app.inactive')</option>
                                </x-forms.select>
                            </div>
                        @else
                            <div class="col-md-3">
                                <x-forms.label :fieldLabel="__('app.status')" fieldId="status"  class="my-3" />
                                <div><i class="fa fa-circle mr-1 text-success f-10"></i> {{__('app.'.$superAdmin->status)}}</div>
                            </div>
                        @endif
                        </div>
                    </div>

                    <div class="col-lg-3 col-xl-2">

                        <x-forms.file allowedFileExtensions="png jpg jpeg svg" class="mr-0 mr-lg-2 mr-md-2 cropper"
                                      :fieldLabel="__('modules.profile.uploadPicture')"
                                      fieldName="image"
                                      fieldId="image"
                                      :fieldValue="$superAdmin->masked_image_url"
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
                url: "{{ route('superadmin.superadmin.update', $superAdmin->id) }}",
                container: '#save-superadmin-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                file: true,
                buttonSelector: "#save-superadmin-form",
                data: $('#save-superadmin-data-form').serialize(),
            });
        });


        $('#random_password').click(function () {
            const randPassword = Math.random().toString(36).substr(2, 8);

            $('#password').val(randPassword);
        });

        $('.cropper').on('dropify.fileReady', function (e) {
            var inputId = $(this).find('input').attr('id');
            var url = "{{ route('cropper', ':element') }}";
            url = url.replace(':element', inputId);
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        init(RIGHT_MODAL);
    });


</script>
