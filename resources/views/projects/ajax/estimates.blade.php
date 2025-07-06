@php
$addEstimatePermission = user()->permission('add_estimates');
@endphp

<!-- ROW START -->
<div class="row py-3 py-lg-5 py-md-5">

    @if (is_null($project->client_id))
        <div class="col-lg-12 col-md-12">
            <x-cards.no-record icon="user" :message="__('messages.assignClientFirst')" />
        </div>
    @else
        <div class="col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4">
            <!-- Add Task Export Buttons Start -->
            <div class="d-flex" id="table-actions">
                @if (($addEstimatePermission == 'all' || $addEstimatePermission == 'added') && !$project->trashed())
                    <x-forms.link-primary
                        :link="route('estimates.create').'?project_id='.$project->id.'&default_client='.$project->client_id"
                        class="mr-3 openRightModal" icon="plus" data-redirect-url="{{ url()->full() }}">
                        @lang('modules.estimates.createEstimate')
                    </x-forms.link-primary>
                @endif
            </div>
            <!-- Add Task Export Buttons End -->

            <form action="" id="filter-form">
                <div class="d-block d-lg-flex d-md-flex my-3">
                    <!-- STATUS START -->
                    <div class="select-box py-2 px-0 mr-3">
                        <x-forms.label :fieldLabel="__('app.status')" fieldId="status" />
                        <select class="form-control select-picker" name="status" id="status" data-live-search="true"
                            data-size="8">
                            <option value="all">@lang('app.all')</option>
                            <option value="waiting">@lang('modules.estimates.waiting')</option>
                            <option value="accepted">@lang('modules.estimates.accepted')</option>
                            <option value="declined">@lang('modules.estimates.declined')</option>
                            <option value="draft">@lang('modules.estimates.draft')</option>
                        </select>
                    </div>
                    <!-- STATUS END -->

                    <!-- SEARCH BY TASK START -->
                    <div class="select-box py-2 px-lg-2 px-md-2 px-0 mr-3">
                        <x-forms.label fieldId="status" />
                        <div class="input-group bg-grey rounded">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-additional-grey">
                                    <i class="fa fa-search f-13 text-dark-grey"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control f-14 p-1 height-35 border" id="search-text-field"
                                placeholder="@lang('app.startTyping')">
                        </div>
                    </div>
                    <!-- SEARCH BY TASK END -->

                    <!-- RESET START -->
                    <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 mt-4">
                        <x-forms.button-secondary class="btn-xs d-none height-35 mt-2" id="reset-filters"
                            icon="times-circle">
                            @lang('app.clearFilters')
                        </x-forms.button-secondary>
                    </div>
                    <!-- RESET END -->
                </div>
            </form>

            <!-- Task Box Start -->
            <div class="d-flex flex-column w-tables rounded mt-3 bg-white">

                {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}

            </div>
            <!-- Task Box End -->

        </div>
    @endif


</div>
<!-- ROW END -->

@include('sections.datatable_js')

<script>
    $('#invoices-table').on('preXhr.dt', function(e, settings, data) {

        var projectID = "{{ $project->id }}";
        var status = $('#status').val();
        var searchText = $('#search-text-field').val();

        data['projectID'] = projectID;
        data['status'] = status;
        data['searchText'] = searchText;
    });
    const showTable = () => {
        window.LaravelDataTables["invoices-table"].draw(true);
    }


    $('#search-text-field').on('keyup', function() {
        if ($('#search-text-field').val() != "") {
            $('#reset-filters').removeClass('d-none');
            showTable();
        }
    });

    $('#reset-filters,#reset-filters-2').click(function() {
        $('#filter-form')[0].reset();

        $('.filter-box .select-picker').selectpicker("refresh");
        $('#reset-filters').addClass('d-none');
        showTable();
    });


    $('#clientID, #project_id, #status')
        .on('change keyup',
            function() {
                if ($('#project_id').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                    showTable();
                } else if ($('#status').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                    showTable();
                } else if ($('#clientID').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                    showTable();
                } else {
                    $('#reset-filters').addClass('d-none');
                    showTable();
                }
            });

    $('#search-text-field').on('keyup', function() {
        if ($('#search-text-field').val() != "") {
            $('#reset-filters').removeClass('d-none');
            showTable();
        }
    });


    $('body').on('click', '.delete-table-row', function() {
        var id = $(this).data('estimate-id');
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
                var url = "{{ route('estimates.destroy', ':id') }}";
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

    $('body').on('click', '.sendButton', function() {
        var id = $(this).data('estimate-id');
        var url = "{{ route('estimates.send_estimate', ':id') }}";
        url = url.replace(':id', id);

        var token = "{{ csrf_token() }}";

        $.easyAjax({
            type: 'POST',
            url: url,
            container: '#invoices-table',
            blockUI: true,
            data: {
                '_token': token
            },
            success: function(response) {
                if (response.status == "success") {
                    window.LaravelDataTables["invoices-table"].draw(true);
                }
            }
        });
    });
</script>
