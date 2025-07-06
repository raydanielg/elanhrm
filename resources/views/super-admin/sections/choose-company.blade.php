@if(!user()->is_superadmin)
@if (count($userCompanies) > 1)
    <hr class="my-1">
    @foreach ($userCompanies as $value)
        <a href="javascript:;" class="dropdown-item align-items-center f-15 text-dark choose-workspace py-2"
            data-user-id="{{ $value->id }}" data-company-id="{{ $value->company->id }}">
            <div class="d-flex bd-highlight">
                <div class="bd-highlight align-self-center">
                    <img src="{{ $value->company->logo_url }}" class="border height-35 width-35 rounded" />
                </div>
                <div class="mr-auto px-3 bd-highlight align-self-center text-truncate">
                    <span class="heading-h5">{{ $value->company->company_name }}</span>
                </div>
                @if (company()->id == $value->company->id)
                    <div class="text-right bd-highlight align-self-center">
                        <i class="bi bi-check2"></i>
                    </div>
                @endif
            </div>
        </a>
    @endforeach
@endif

<script>
    $('.choose-workspace').click(function() {

        var url = "{{ route('superadmin.superadmin.choose_workspace') }}";
        var token = "{{ csrf_token() }}";
        var userId = $(this).data('user-id');
        var companyId = $(this).data('company-id');

        $.easyAjax({
            url: url,
            container: '#body',
            type: "POST",
            blockUI: true,
            data: {
                user_id : userId,
                company_id : companyId,
                _token: token
            },
            success: function(response) {
                if (response.status == 'success') {
                    window.location.href = response.redirect_url;
                }
            }
        })
    });
</script>
@endif
