@extends('layouts.app')

@section('content')

    <!-- SETTINGS START -->
    <div class="w-100 d-flex ">

        <x-super-admin.front-setting-sidebar :activeMenu="$activeSettingMenu" />

        <x-setting-card>
            <x-slot name="header">
                <div class="s-b-n-header" id="tabs">
                    <nav class="tabs px-4 border-bottom-grey">
                        <div class="nav" id="nav-tab" role="tablist">

                            <a class="nav-item nav-link f-15 active setting" href="{{ route('superadmin.front-settings.testimonial-settings.index') }}"
                               role="tab" aria-controls="nav-ticketAgents"
                               aria-selected="true">@lang('superadmin.menu.testimonial')
                            </a>

                            <a class="nav-item nav-link f-15 title" href="{{ route('superadmin.front-settings.testimonial-settings.index') }}?tab=title"
                               role="tab" aria-controls="nav-ticketTypes"
                               aria-selected="true">@lang('superadmin.menu.testimonial') @lang('app.title')
                            </a>

                        </div>
                    </nav>
                </div>
            </x-slot>

            <x-slot name="buttons">
                <div class="row">

                    <div class="col-md-12 mb-2">
                        <x-forms.button-primary icon="plus" id="addTestimonial" class="setting-btn mb-2 d-none actionBtn">
                            @lang('app.addNew') @lang('superadmin.menu.testimonial')
                        </x-forms.button-primary>

                        <x-forms.button-primary icon="plus" id="addTestimonialTitle" class="title-btn mb-2 d-none actionBtn">
                            @lang('app.addNew')
                            @lang('superadmin.menu.testimonial') @lang('app.title')
                        </x-forms.button-primary>

                    </div>

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

        showBtn(activeTab);

        function showBtn(activeTab) {
            $('.actionBtn').addClass('d-none');
            $('.' + activeTab + '-btn').removeClass('d-none');
        }


        /* testimonial */
        $('body').on('click', '.delete-testimonial', function() {
            var id = $(this).data('testimonial-id');
            Swal.fire({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('superadmin.messages.removeTestimonialText')",
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
                    var url = "{{ route('superadmin.front-settings.testimonial-settings.destroy', ':id') }}";
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
                                $('.row' + id).fadeOut(100);
                            }
                        }
                    });
                }
            });
        });

        $('body').on('click', '.edit-testimonial', function() {
            var typeId = $(this).data('testimonial-id');
            var url = "{{ route('superadmin.front-settings.testimonial-settings.edit', ':id') }}";
            url = url.replace(':id', typeId);

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        /* testimonial title */

        $('body').on('click', '.edit-testimonial-title', function() {
            var typeId = $(this).data('title-id');
            var url = "{{ route('superadmin.front-settings.edit_testimonial_title', ':id') }}";
            url = url.replace(':id', typeId);

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });


        /* open add Testimonial modal */
        $('body').on('click', '#addTestimonial', function() {
            var url = "{{ route('superadmin.front-settings.testimonial-settings.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        /* open add Testimonial Title modal */
        $('body').on('click', '#addTestimonialTitle', function() {
            var url = "{{ route('superadmin.front-settings.create_testimonial_title') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

    </script>
@endpush
