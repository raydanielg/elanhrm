<x-table class="table-sm-responsive mb-0">
    <x-slot name="thead">
        <th>@lang('app.title')</th>
        <th>@lang('app.description')</th>
        <th>@lang('superadmin.changeToPosition')</th>
        <th>@lang('app.status')</th>
        <th>@lang('superadmin.footerSettings.private')
            <i class="fa fa-question-circle" data-toggle="popover" data-placement="top"
               data-content="{{ __('superadmin.footerSettingPageType') }}" data-html="true"
               data-trigger="hover"></i>
        </th>
        <th>@lang('superadmin.footerSettings.pageLink')</th>
        <th class="text-right">@lang('app.action')</th>
    </x-slot>

    @forelse($footer as $footerMenu)
        <tr class="row{{ $footerMenu->id }}">
            <td>{{ $footerMenu->name }}</td>
            <td>
                @if(!is_null($footerMenu->description))
                    {!!   str_limit($footerMenu->description, 50) !!}
                @else
                    <a target="_blank"
                       href="{{ $footerMenu->external_link }}">{{ $footerMenu->external_link }}</a>
                @endif
            </td>
            <td>
                @if ($footerMenu->type == 'footer')
                    @lang('superadmin.footer.footer')
                @elseif ($footerMenu->type == 'header')
                    @lang('superadmin.header')
                @else
                    @lang('superadmin.headerFooterBoth')
                @endif
            </td>

            <td>
                @if ($footerMenu->status == 'active')
                    <i class="fa fa-circle mr-1 text-light-green f-10"></i>@lang('app.active')
                @else
                    <i class="fa fa-circle mr-1 text-red f-10"></i>@lang('app.inactive')
                @endif
            </td>
            <td>
                @if ($footerMenu->private == 1)
                    @lang('app.yes')
                @else
                    @lang('app.no')
                @endif
            </td>
            <td>


                <button type="button" data-clipboard-text="{{ route('front.page', $footerMenu->slug) }}"
                        data-toggle="tooltip"
                        data-clipboard-action="copy"
                        data-original-title="@lang('superadmin.footerSettings.copyLink')"
                        class="btn-copy-cron btn btn-sm btn-secondary p-1 f-10">
                    <i class="fa fa-copy "></i>
                </button>
            </td>
            <td class="text-right">
                <div class="task_view">
                    <a class="task_view_more d-flex align-items-center justify-content-center edit-footer"
                       href="javascript:;" data-id="{{ $footerMenu->id }}">
                        <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                    </a>
                </div>
                <div class="task_view mt-1 mt-lg-0 mt-md-0">
                    <a class="task_view_more d-flex align-items-center justify-content-center delete-table-row"
                       href="javascript:;" data-id="{{ $footerMenu->id }}">
                        <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                    </a>
                </div>
            </td>
        </tr>
    @empty
        <x-cards.no-record-found-list colspan="6"/>
    @endforelse

</x-table>
<script src="{{ asset('vendor/jquery/clipboard.min.js') }}"></script>

<script>
        /* open add footer modal */
        $('.edit-footer').click(function () {
            var id = $(this).data('id');
            var lang = $('#language').val();

            var url = "{{ route('superadmin.front-settings.footer-settings.edit', ':id')}}?lang=" + lang;
            url = url.replace(':id', id);

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

</script>
