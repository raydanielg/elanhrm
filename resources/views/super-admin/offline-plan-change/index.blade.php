@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('content')

    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <div class="d-flex flex-column w-tables rounded mt-3 bg-white">

            {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}

        </div>
    </div>
    <!-- CONTENT WRAPPER END -->

@endsection

@push('scripts')
    @include('sections.datatable_js')
    <script>
        const showTable = () => {
            window.LaravelDataTables["offline-plan-change-table"].draw(true);
        }
        $(document).ready(function () {
            showTable();
        });

        $('body').on('click', '.change-status', function() {
            var planId = $(this).data('id');
            var status = $(this).data('status');
            var url = "{{ route('superadmin.offline-plan.confirmChangePlan', [':id', ':status']) }}";
            url = url.replace(':status', status);
            url = url.replace(':id', planId);
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
    </script>
@endpush
