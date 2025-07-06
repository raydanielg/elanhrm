@extends('layouts.app')

@section('content')

    <!-- SETTINGS START -->
    <div class="w-100 d-flex ">

        <x-setting-sidebar :activeMenu="$activeSettingMenu" />

        <x-setting-card>

            <x-slot name="alert">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    <?php Session::forget('success');?>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    <?php Session::forget('error');?>
                @endif
            </x-slot>

            <x-slot name="header" >
                <div class="s-b-n-header" id="tabs">
                    <nav class="tabs px-4 border-bottom-grey">
                        <div class="nav" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link f-15 active plan" href="{{ route('billing.index') }}" role="tab" aria-controls="nav-plan" aria-selected="true">@lang('superadmin.menu.planDetails')
                            </a>
                            <a class="nav-item nav-link f-15 purchase-history" href="{{route('billing.index')}}?tab=purchase-history" role="tab" aria-controls="nav-purchase" aria-selected="true" ajax="false"> @lang('superadmin.menu.purchaseHistory')
                            </a>
                            <a class="nav-item nav-link f-15 offline-request" href="{{route('billing.index')}}?tab=offline-request" role="tab" aria-controls="nav-offline" aria-selected="true" ajax="false"> @lang('superadmin.menu.offlineRequest')
                            </a>
                        </div>
                    </nav>
                </div>
            </x-slot>

            {{-- include tabs here --}}
            @include($view)

        </x-setting-card>

    </div>
    <!-- SETTINGS END -->

@endsection

@push('scripts')
<script>

    /* manage menu active class */
    $('.nav-item').removeClass('active');
    const activeTab = "{{ $activeTab }}";
    $('.' + activeTab).addClass('active');

    // Delete currency
    $('body').on('click', '.delete-table-row', function() {
        var id = $(this).data('currency-id');
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
                let url = "{{ route('currency-settings.destroy', ':id') }}";
                url = url.replace(':id', id);

                const token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    blockUI: true,
                    data: {
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function(response) {
                        if (response.status === "success") {
                            $('.row'+id).fadeOut();
                        }
                    }
                });
            }
        });
    });

</script>
@endpush
