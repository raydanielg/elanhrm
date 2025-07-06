
<!-- ROW START -->
<div class="row pb-5">
    <div class="col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4">
        <!-- Task Box Start -->
        <div class="d-flex flex-column w-tables rounded mt-3 bg-white">

            {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}

        </div>
        <!-- Task Box End -->
    </div>
</div>

@include('sections.datatable_js')

<script>
    $('#invoice-table').on('preXhr.dt', function(e, settings, data) {
        var searchText = $('#search-text-field').val();
        var company_id = "{{ $company->id }}";
        data['company_id'] = company_id;
        data['searchText'] = searchText;
    });

    const showTable = () => {
        window.LaravelDataTables["invoice-table"].draw();
    }

    $('#search-text-field, #company_id')
        .on('change keyup',
            function() {
                if ($('#search-text-field').val() != "") {
                    $('#reset-filters').removeClass('d-none');
                    showTable();
                }  else if ($('#company_id').val() !== "") {
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

</script>
