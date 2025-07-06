@forelse($faqs as $faq)
    <x-cards.data class="mb-3" :title="$faq->question">
        <x-slot name="action">
            <div>
                <div class="task_view">
                    <a class="task_view_more d-flex align-items-center justify-content-center edit-faq"
                        href="javascript:;" data-id="{{ $faq->id }}">
                        <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                    </a>
                </div>
                <div class="task_view mt-1 mt-lg-0 mt-md-0">
                    <a class="task_view_more d-flex align-items-center justify-content-center delete-table-row"
                        href="javascript:;" data-id="{{ $faq->id }}">
                        <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                    </a>
                </div>
            </div>
        </x-slot>

        <div class="ql-editor p-0">
            {!! $faq->answer !!}
        </div>

    </x-cards.data>
@empty
    <x-cards.no-record icon="list" :message="__('messages.noRecordFound')" />
@endforelse
