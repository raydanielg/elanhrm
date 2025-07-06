@extends('layouts.app')
@section('content')
    <!-- SETTINGS START -->
    <div class="w-100 d-flex">

        <x-super-admin.front-setting-sidebar :activeMenu="$activeSettingMenu"/>

        <x-setting-card>

            <x-slot name="header">
                <div class="s-b-n-header" id="tabs">
                    <h2 class="f-21 font-weight-normal text-capitalize border-bottom-grey mb-0 p-20">
                        @lang($pageTitle)</h2>
                </div>
            </x-slot>
            <!-- LEAVE SETTING START -->
            <div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-0">

                <x-table class="table-sm-responsive table mb-0">
                    <x-slot name="thead">
                        <th>@lang('app.name')</th>
                        <th>@lang("superadmin.frontCms.seo_title")</th>
                        <th>@lang("superadmin.frontCms.seo_author")</th>
                        <th>@lang("superadmin.frontCms.seo_description")</th>
                        <th>@lang("superadmin.frontCms.seo_keywords")</th>
                        <th class="text-right">@lang('app.action')</th>
                    </x-slot>

                    @forelse($seoDetails as $seoDetail)
                        <tr>
                            <td>{{ $seoDetail->page_name }}</td>
                            <td>{{ $seoDetail->seo_title }}</td>
                            <td>{{ $seoDetail->seo_author }}</td>
                            <td>{!! mb_strimwidth($seoDetail->seo_description, 0, 50, '...')  !!}</td>
                            <td>{!! mb_strimwidth($seoDetail->seo_keywords, 0, 50, '...')  !!}</td>
                            <td class="text-right">
                                <div class="task_view">
                                    <a class="task_view_more d-flex align-items-center justify-content-center edit-seo "
                                    data-id="{{ $seoDetail->id }}"
                                       href="javascript:;">
                                        <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-cards.no-record-found-list/>
                    @endforelse

                </x-table>

            </div>
            <!-- LEAVE SETTING END -->
        </x-setting-card>

    </div>
    <!-- SETTINGS END -->
@endsection

@push('scripts')
<script>
    /* open add front client modal */
    $('body').on('click', '.edit-seo', function () {
            var id = $(this).data('id');
            var url = "{{ route('superadmin.front-settings.seo-detail.edit', [':id']) }}";
            url = url.replace(':id', id);

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
</script>

@endpush
