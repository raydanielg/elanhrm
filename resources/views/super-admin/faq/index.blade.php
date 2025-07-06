@extends('layouts.app')

@section('content')

@php
$addFaqPermission = user()->permission('add_admin_faq');
$manageFaqCategoryPermission = user()->permission('manage_faq_category');
$viewFaqPermission = user()->permission('view_admin_faq');
$deleteFaqPermission = user()->permission('delete_admin_faq');
@endphp
    <!-- SETTINGS START -->
    <div class="w-100 d-flex ">

        <!-- SETTINGS SIDEBAR START -->
        <div class="mobile-close-overlay w-100 h-100" id="close-settings-overlay"></div>
        <div class="settings-sidebar bg-white py-3" id="mob-settings-sidebar">
            <a class="d-block d-lg-none close-it" id="close-settings"><i class="fa fa-times"></i></a>

            <!-- SETTINGS SEARCH START -->
            <form class="border-bottom-grey px-4 pb-3 d-flex">
                <div class="input-group rounded py-1 border-grey">
                    <div class="input-group-prepend">
                        <span class="input-group-text border-0 bg-white">
                            <i class="fa fa-search f-12 text-lightest"></i>
                        </span>
                    </div>
                    <input type="text" id="search-setting-menu" class="form-control border-0 f-14 pl-0"
                        placeholder="@lang('app.search')">
                </div>
            </form>
            <!-- SETTINGS SEARCH END -->

            <!-- SETTINGS MENU START -->
            <ul class="settings-menu" id="settingsMenu">

                <x-setting-menu-item :active="$activeMenu" menu="all_category" :href="route('superadmin.faqs.index')" :text="__('app.all')" />

                @foreach ($categories as $item)
                    <x-setting-menu-item :id="'category-' . $item->id" :active="$activeMenu" :menu="strtolower(str_replace(' ', '_', $item->name))" :href="route('superadmin.faqs.index') . '?id=' . $item->id"
                        :text="$item->name" />
                @endforeach

            </ul>
            <!-- SETTINGS MENU END -->

        </div>
        <!-- SETTINGS SIDEBAR END -->

        <x-setting-card>

            <x-slot name="buttons">
                <form action="" id="filter-form">
                    <div class="d-lg-flex justify-conten mb-2">

                        <div class="form-group flex-grow-1">
                            @if (user()->is_superadmin)
                                @if ($addFaqPermission == 'all')
                                    <x-forms.link-primary :link="route('superadmin.faqs.create', ['id'=> request()->id])" class="mr-3 mb-2 float-left openRightModal" icon="plus">
                                        @lang('superadmin.addAdminFaq')
                                    </x-forms.link-primary>
                                @endif
                                @if($manageFaqCategoryPermission == 'all')
                                    <x-forms.button-secondary id="manage-category" class="mr-3 mb-2 mb-lg-0" icon="plus">
                                        @lang('app.manage') @lang('app.category')
                                    </x-forms.button-secondary>
                                @endif
                            @elseif(in_array('admin', user_roles()))
                                <x-forms.link-primary :link="route('superadmin.support-tickets.index')" class="mr-3 mb-2 float-left" icon="headset">
                                    @lang('superadmin.contactSupport')
                                </x-forms.link-primary>
                            @endif
                        </div>

                        <div class="form-group">
                            <div class="input-group bg-grey rounded border">
                                <div class="input-group-prepend">
                                    <span class="input-group-text border-0">
                                        <i class="fa fa-search f-13 text-dark-grey"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control height-35 f-14 p-1 border-additional-grey"
                                    id="search-text-field" placeholder="@lang('app.startTyping')">
                            </div>
                        </div>
                        <x-forms.button-secondary class="btn-xs d-none height-35 ml-2" id="reset-filters"
                            icon="times-circle">
                            @lang('app.clearFilters')
                        </x-forms.button-secondary>
                    </div>
                </form>
            </x-slot>

            <x-slot name="header">
                <div class="s-b-n-header" id="tabs">
                    <h2 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                        @lang($pageTitle)</h2>
                </div>
            </x-slot>

            <div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-0 pt-2 ">
                <div class="table-responsive" id="know_data">
                    <x-table class="table-bordered">
                        <x-slot name="thead">
                            <th>#</th>
                            <th>@lang('modules.knowledgeBase.knowledgeHeading')</th>
                            <th>@lang('modules.knowledgeBase.knowledgeCategory')</th>
                            @if (user()->is_superadmin)
                                <th class="text-right">@lang('app.action')</th>
                            @endif
                        </x-slot>

                        @forelse ($knowledgebases as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <a href="{{ route('superadmin.faqs.show', $item->id) }}"
                                        class="openRightModal text-darkest-grey d-block">{{ $item->title }}</a>
                                </td>
                                <td>{{ $item->category->name }}</td>
                                @if (user()->is_superadmin)
                                    <td class="text-right">
                                        <div class="task_view">
                                            <a href="{{ route('superadmin.faqs.edit', $item->id) }}"
                                                class="task_view_more d-flex align-items-center justify-content-center openRightModal">
                                                <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                                            </a>
                                        </div>
                                        <div class="task_view ml-2">
                                            <a href="javascript:;" data-article-id="{{ $item->id }}"
                                                class="task_view_more d-flex align-items-center justify-content-center delete-article">
                                                <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                                            </a>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-cards.no-record icon="list" :message="__('messages.noRecordFound')" />
                                </td>
                            </tr>
                        @endforelse
                    </x-table>
                </div>
            </div>
        </x-setting-card>

    </div>
    <!-- SETTINGS END -->
@endsection
@push('scripts')
    <script>
        $('#search-text-field').on('change keyup', function() {
            if ($('#search-text-field').val() != "") {
                $('#reset-filters').removeClass('d-none');
            } else {
                $('#reset-filters').addClass('d-none');
            }
        });

        $('#reset-filters').click(function() {
            $('#filter-form')[0].reset();
            $('.select-picker').val('all');

            $('.select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');

            showSearchData();
        });

        $('#quick-action-type').change(function() {
            const actionValue = $(this).val();
            if (actionValue != '') {
                $('#quick-action-apply').removeAttr('disabled');
            } else {
                $('#quick-action-apply').attr('disabled', true);
            }
        });

        function showSearchData() {
            var srch = $('#search-text-field').val();
            var url = "{{ route('superadmin.faqs.searchQuery', ':query') }}";
            url = url.replace(':query', srch);

            var token = "{{ csrf_token() }}";
            var categoryId = "{{ request()->id }}";

            $.easyAjax({
                type: 'GET',
                url: url,
                data: {
                    '_token': token,
                    'categoryId': categoryId
                },
                success: function(response) {
                    if (response.status == "success") {
                        $("#know_data").html(response.html);
                    }
                }
            });
        }

        $('#search-text-field').on('change keyup', function() {
            showSearchData();
        });


        $("#search-setting-menu").on("keyup", function() {
            var value = this.value.toLowerCase().trim();
            $("#settingsMenu li").show().filter(function() {
                return $(this).text().toLowerCase().trim().indexOf(value) == -1;
            }).hide();
        });

        @if (user()->is_superadmin)
        $('body').on('click', '.delete-article', function() {
            var articleId = $(this).data('article-id');

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
                    var url = "{{ route('superadmin.faqs.destroy', ':id') }}";
                    url = url.replace(':id', articleId)

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
                                window.location.reload();
                            }
                        }
                    });
                }
            });
        });
        @endif

        $('#reset-filters').click(function() {
            $('#filter-form')[0].reset();
            $('.select-picker').val('all');

            $('.select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');

            showSearchData();
        });

        @if (user()->is_superadmin)
        $('#manage-category').click(function() {
            const url = "{{ route('superadmin.faqCategory.create') }}";
            $(`${MODAL_LG} ${MODAL_HEADING}`).html('...');
            $.ajaxModal(MODAL_LG, url);
        })
        @endif
    </script>
@endpush
