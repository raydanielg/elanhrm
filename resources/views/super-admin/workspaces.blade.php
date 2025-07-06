@push('styles')
    <style>
        .disabled {
            cursor: not-allowed;
            opacity: 0.5;
            text-decoration: none;
        }

        .login_box a span {
            background-color: unset;
        }
    </style>
@endpush
<x-auth>
    <x-form id="acceptInviteForm">
        <div class="mx-auto">

            <h4 class="text-center mb-3 f-w-500">@lang('app.welcome') {{ user()->name }}!</h4>

            <div class="text-center mb-3 f-13">@lang('superadmin.chooseWorkspace')</div>

            <div class="px-4 py-3">@lang('superadmin.workspacesFor') <strong>{{ user()->email }}</strong></div>

            @foreach ($userCompanies as $userCompany)
                @if ($userCompany->company->status == 'active')
                <a href="javascript:;"
                class="border-0 text-dark-grey justify-content-between d-inline {{ ($userCompany->login == 'disable' || !$userCompany->company->approved) ? 'disabled' : 'choose-workspace'}}"
                data-user-id="{{ $userCompany->id }}" data-company-id="{{ $userCompany->company->id }}"
                @if ($userCompany->login == 'disable' || !$userCompany->company->approved)
                    data-toggle="tooltip" data-original-title="@lang('superadmin.loginRestricted')"
                    @endif
                >
                    <div class="d-flex bd-highlight py-3">
                        <div class="bd-highlight align-self-center">
                            <img src="{{ $userCompany->company->logo_url }}"
                                class="img-thumbnail height-50 rounded"/>
                        </div>
                        <div class="mr-auto px-3 bd-highlight align-self-center">
                                <span
                                    class="heading-h3">{{ $userCompany->company->company_name }}</span>
                            <span class="f-14">{{ $userCompany->name }}</span>
                        </div>
                        <div class="bd-highlight align-self-center">
                            <span><i class="fa fa-arrow-right"></i></span>
                        </div>
                    </div>
                </a>
                @endif
            @endforeach

            <div class="forgot_pswd text-center mt-3">
                <a href="{{ route('logout') }}" onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();" class="justify-content-center">
                    @lang('superadmin.loginWithDifferentEmail')
                </a>
            </div>
        </div>
    </x-form>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <x-slot name="scripts">
        <script>
            $('.choose-workspace').click(function () {

                var url = "{{ route('superadmin.superadmin.choose_workspace') }}";
                var token = "{{ csrf_token() }}";
                var userId = $(this).data('user-id');
                var companyId = $(this).data('company-id');

                $.easyAjax({
                    url: url,
                    container: '#acceptInviteForm',
                    type: "POST",
                    blockUI: true,
                    data: {
                        user_id: userId,
                        company_id: companyId,
                        _token: token
                    },
                    success: function (response) {
                        if (response.status == 'success') {
                            window.location.href = response.redirect_url;
                        }
                    }
                })
            });
        </script>
    </x-slot>

</x-auth>
