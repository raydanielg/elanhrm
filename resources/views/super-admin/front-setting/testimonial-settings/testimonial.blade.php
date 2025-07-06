
<div class="table-responsive p-20">
    <x-table class="table-bordered">
        <x-slot name="thead">
            <th>@lang('app.name')</th>
            <th>@lang('app.comment')</th>
            <th>@lang('app.language')</th>
            <th>@lang('app.rating')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>

        @forelse($testimonials as $testimonial)
            <tr class="row{{ $testimonial->id }}">
                <td>{{ $testimonial->name }}</td>
                <td>{!! nl2br($testimonial->comment)  !!}</td>
                <td>{{ $testimonial->language ? $testimonial->language->language_name : 'English' }}</td>
                <td>{{ $testimonial->rating }}</td>
                <td class="text-right">
                    <div class="task_view">
                        <a class="task_view_more d-flex align-items-center justify-content-center edit-testimonial" data-testimonial-id="{{ $testimonial->id }}" href="javascript:;" >
                            <i class="fa fa-edit icons mr-2"></i>  @lang('app.edit')
                        </a>
                    </div>
                    <div class="task_view mt-1 mt-lg-0 mt-md-0">
                        <a class="task_view_more d-flex align-items-center justify-content-center delete-table-row delete-testimonial" href="javascript:;" data-testimonial-id="{{ $testimonial->id }}">
                            <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
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
