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
