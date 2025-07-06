<x-auth>
    <x-form id="notify-admin" action="#" class="ajax-form">

        @if ($isNotified)
            <div class="alert alert-success">
                @lang('superadmin.packageIssueNotified')
            </div>
        @else
            <div id="alert" class="text-center">
                <h3 class="mb-1 f-w-500">@lang('superadmin.issueWithCompany')</h3>
                <h4 class="mb-4 mt-3 heading-h4 text-danger">@lang('superadmin.issueWithCompanyText')</h4>
                <button type="button" id="submit-notify-admin"
                    class="btn-primary f-w-500 rounded w-100 height-50 f-18">
                    @lang('superadmin.issueNotifyButton')
                </button>
            </div>
        @endif

    </x-form>


    <x-slot name="scripts">
        <script>
            $(document).ready(function () {

                $('#submit-notify-admin').click(function () {
                    $.easyAjax({
                        url: "{{ route('superadmin.notify.admin.submit') }}",
                        container: '.login_box',
                        disableButton: true,
                        buttonSelector: "#submit-notify-admin",
                        type: "POST",
                        blockUI: true,
                        data: $('#notify-admin').serialize(),
                        messagePosition: "inline",
                    });
                });

            });
        </script>
    </x-slot>

</x-auth>
