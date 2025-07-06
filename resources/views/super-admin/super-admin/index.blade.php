@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')

    <x-filters.filter-box>
        <!-- SEARCH BY TASK START -->
        <div class="task-search d-flex py-1 pr-lg-3 px-0 border-right-grey align-items-center">
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
@php
    $addSuperadminPermission = user()->permission('add_superadmin');
@endphp
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <!-- Add Task Export Buttons Start -->
        <div class="d-flex">
            <div id="table-actions" class="flex-grow-1 align-items-center">
                @if($addSuperadminPermission == 'all')
                <x-forms.link-primary :link="route('superadmin.superadmin.create')" class="mr-3 float-left openRightModal"
                    icon="plus">
                    @lang('superadmin.addSuperAdmin')
                </x-forms.link-primary>
                @endif
            </div>
        </div>

        <!-- Add Task Export Buttons End -->
        <!-- Task Box Start -->
        <div class="d-flex flex-column w-tables rounded mt-3 bg-white">

            {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}

        </div>
        <!-- Task Box End -->
    </div>
    <!-- CONTENT WRAPPER END -->

@endsection

@push('scripts')
    @include('sections.datatable_js')

    <script>
        $('#superadmin-table').on('preXhr.dt', function(e, settings, data) {
            var searchText = $('#search-text-field').val();
            data['searchText'] = searchText;
        });

        const showTable = () => {
            window.LaravelDataTables["superadmin-table"].draw();
        }

        $('#search-text-field')
            .on('change keyup',
                function() {
                    if ($('#search-text-field').val() != "") {
                        $('#reset-filters').removeClass('d-none');
                        showTable();
                    } else {
                        $('#reset-filters').addClass('d-none');
                        showTable();
                    }
                });

        $('#reset-filters').click(function() {
            $('#filter-form')[0].reset();

            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
        });

        $('#reset-filters-2').click(function() {
            $('#filter-form')[0].reset();

            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
        });

        $('body').on('click', '.delete-table-row', function() {
            var id = $(this).data('superadmin-id');
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
                    var url = "{{ route('superadmin.superadmin.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        blockUI: true,
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

        $('body').on('change', '.assign_role', function () {
            var id = $(this).data('user-id');
            var role = $(this).val();
            var token = "{{ csrf_token() }}";

            if (typeof id !== 'undefined') {
                $.easyAjax({
                    url: "{{ route('superadmin.superadmin.assign_role') }}",
                    type: "POST",
                    blockUI: true,
                    container: '#superadmin-table',
                    data: {
                        role: role,
                        userId: id,
                        _token: token
                    },
                    success: function (response) {
                        if (response.status == "success") {
                            window.LaravelDataTables["superadmin-table"].draw(true);
                        }
                    }
                })
            }

        });


    </script>
@endpush
