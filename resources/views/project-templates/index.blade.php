@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')

    <x-filters.filter-box>

        <!-- SEARCH BY TASK START -->
        <div class="task-search d-flex  py-1 px-0 border-right-grey align-items-center">
            <form class="w-100 mr-1 mr-lg-0 mr-md-1 ml-md-1 ml-0 ml-lg-0">
                <div class="input-group bg-grey rounded">
                    <div class="input-group-prepend">
                        <span class="input-group-text border-0 bg-additional-grey">
                            <i class="fa fa-search f-13 text-dark-grey"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control f-14 p-1 border-additional-grey" id="search-text-field"
                        placeholder="@lang('app.startTyping')">
                </div>
            </form>
        </div>
        <!-- SEARCH BY TASK END -->
        <div class="select-box d-flex py-2 {{ !in_array('client', user_roles()) ? 'px-lg-2 px-md-2 px-0' : '' }}  border-right-grey border-right-grey-sm-0 pr-2 pl-2">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('modules.projects.projectCategory')</p>
            <div class="select-status w-80">
                <select class="form-control select-picker" name="project_template_category_id" id="project_template_category_id" data-live-search="true" data-size="8">
                    
                    <option value="all" >@lang('app.all')</option>
                    @foreach ($categories as $category)
                        <option value="{{$category->id}}">{{ $category->category_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="select-box d-flex py-2 {{ !in_array('client', user_roles()) ? 'px-lg-2 px-md-2 px-0' : '' }}  border-right-grey border-right-grey-sm-0 pr-2 pl-2">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('modules.client.projectSubCategory')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="project_template_sub_category_id" id="project_template_sub_category_id" data-live-search="true" data-size="8">
                    <option value="all" >Select Project Category</option>
                </select>
            </div>
        </div>

        <!-- RESET START -->
        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
        <!-- RESET END -->

    </x-filters.filter-box>

@endsection

@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <div class="d-block d-lg-flex d-md-flex justify-content-between action-bar">
            <!-- Add Task Export Buttons Start -->
            <div class="d-flex" id="table-actions">
                @if(in_array($manageProjectTemplatePermission, ['added', 'all']))
                    <x-forms.link-primary :link="route('project-template.create')" class="mr-3 openRightModal" icon="plus">
                        @lang('app.menu.addProjectTemplate')
                    </x-forms.link-primary>
                @endif
            </div>
            <!-- Add Task Export Buttons End -->

            @if (!in_array('client', user_roles()))
                <x-datatable.actions>
                    <div class="select-status mr-3 pl-3">
                        <select name="action_type" class="form-control select-picker" id="quick-action-type" disabled>
                            <option value="">@lang('app.selectAction')</option>
                            <option value="delete">@lang('app.delete')</option>
                        </select>
                    </div>
                </x-datatable.actions>
            @endif
        </div>
        <!-- Task Box Start -->
        <div class="d-flex flex-column w-tables rounded mt-3 bg-white">

            {!! $dataTable->table(['class' => 'table table-hover border-0']) !!}

        </div>
        <!-- Task Box End -->
    </div>
    <!-- CONTENT WRAPPER END -->

@endsection

@push('scripts')
    @include('sections.datatable_js')

    <script>
        $('#projects-template-table').on('preXhr.dt', function(e, settings, data) {

            var searchText = $('#search-text-field').val();
            var projectCategoryId = $('#project_template_category_id').val();
            var subCategoryId = $('#project_template_sub_category_id').val();

            data['project_category_id'] = projectCategoryId;
            data['sub_category_id'] = subCategoryId;
            data['searchText'] = searchText;
        });
        const showTable = () => {
            window.LaravelDataTables["projects-template-table"].draw(true);
        }

        $('#search-text-field,#project_template_category_id,#project_template_sub_category_id').on('change keyup',
            function() {
                if ($('#search-text-field').val() != "") {
                    $('#reset-filters').removeClass('d-none');
                    showTable();
                } else if ($('#project_template_category_id').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                    showTable();
                } else if ($('#project_template_sub_category_id').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                    showTable();
                } else {
                    $('#reset-filters').addClass('d-none');
                    showTable();
                }
            });

        $('#reset-filters').click(function() {
            $('#filter-form')[0].reset();
            $('.filter-box #status').val('not finished');
            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
        });

        $('#quick-action-type').change(function() {
            const actionValue = $(this).val();
            if (actionValue != '') {
                $('#quick-action-apply').removeAttr('disabled');

                if (actionValue == 'change-status') {
                    $('.quick-action-field').addClass('d-none');
                    $('#change-status-action').removeClass('d-none');
                } else {
                    $('.quick-action-field').addClass('d-none');
                }
            } else {
                $('#quick-action-apply').attr('disabled', true);
                $('.quick-action-field').addClass('d-none');
            }
        });

         // for sub category
        $('#project_template_category_id').change(function(e) {

            let categoryId = $(this).val();
            
            if (categoryId === '') {
                $('#project_template_sub_category_id').html('<option value="">--</option>');
                $('#project_template_sub_category_id').selectpicker('refresh');
                return; // Stop further execution when no category is selected
            }

            var url = "{{ route('project.get_project_sub_category', ':id') }}";
            url = url.replace(':id', categoryId);

            $.easyAjax({
                url: url,
                type: "GET",
                success: function(response) {
                    console.log('categoryId');
                    console.log(categoryId);
                    if (response.status == 'success') {
                        console.log(categoryId);
                        var options = [];
                        var rData = [];
                        rData = response.data;
                        $.each(rData, function(index, value) {
                            var selectData = '';
                            selectData = '<option value="' + value.id + '">' + value
                                .category_name + '</option>';
                            options.push(selectData);
                        });

                        $('#project_template_sub_category_id').html('<option value="all">--</option>' +
                            options);
                        $('#project_template_sub_category_id').selectpicker('refresh');
                    }
                }
            })

        });

        $('#quick-action-apply').click(function() {
            const actionValue = $('#quick-action-type').val();
            if (actionValue == 'delete') {
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
                        applyQuickAction();
                    }
                });

            } else {
                applyQuickAction();
            }
        });

        $('body').on('click', '.delete-table-row', function() {
            var id = $(this).data('user-id');
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
                    var url = "{{ route('project-template.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {
                            '_token': token,
                            '_method': 'DELETE'
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                showTable();
                            }
                        }
                    });
                }
            });
        });

        const applyQuickAction = () => {
            var rowdIds = $("#projects-template-table input:checkbox:checked").map(function() {
                return $(this).val();
            }).get();

            var url = "{{ route('project_template.apply_quick_action') }}?row_ids=" + rowdIds;

            $.easyAjax({
                url: url,
                container: '#quick-action-form',
                type: "POST",
                disableButton: true,
                buttonSelector: "#quick-action-apply",
                data: $('#quick-action-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        showTable();
                        resetActionButtons();
                        deSelectAll();
                    }
                }
            })
        };

    </script>
@endpush
