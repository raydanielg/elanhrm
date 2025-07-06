
<div class="table-responsive p-20">
    <x-table class="table-bordered">
        <x-slot name="thead">
            <th>@lang('app.title')</th>
            <th>@lang('app.language')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>

        @forelse($titles as $title)
            <tr class="row{{ $title->id }}">
                <td>{{ $title->testimonial_title }}</td>
                <td>{{ $title->language ? $title->language->language_name : 'English' }}</td>
                <td class="text-right">
                    <div class="task_view">
                        <a class="task_view_more d-flex align-items-center justify-content-center edit-testimonial-title" data-title-id="{{ $title->id }}" href="javascript:;" >
                            <i class="fa fa-edit icons mr-2"></i>  @lang('app.edit')
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">
                    <x-cards.no-record icon="list" :message="__('messages.noRecordFound')" />
                </td>
            </tr>
        @endforelse
    </x-table>
</div>
