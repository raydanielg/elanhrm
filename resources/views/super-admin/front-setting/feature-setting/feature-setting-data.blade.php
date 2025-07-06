@forelse($featureSettings as $setting)
        <x-table @class([
                'mb-0',
                'mt-4' => !$loop->first,
            ]) >
            <x-slot name="thead">
                <th>@lang('app.title')</th>
                <th>@lang('app.description')</th>
                <th>@lang('app.language')</th>
                <th class="text-right pr-20">@lang('app.action')</th>
            </x-slot>

            <tr class="row{{ $setting->id }}">
                <td>{{ $setting->title }}</td>
                <td>{!! mb_strimwidth($setting->description, 0, 50, '...')  !!}</td>
                <td>{{ $setting->language ? $setting->language->language_name : 'English' }}</td>
                <td class="text-right pr-20">
                    <div class="task_view">
                        <a class="task_view_more d-flex align-items-center justify-content-center edit-feature" data-id="{{$setting->id}}" data-type="{{$type}}">
                            <i class="fa fa-edit icons mr-2"></i>  @lang('app.edit')
                        </a>
                    </div>
                    <div class="task_view mt-1 mt-lg-0 mt-md-0">
                        <a class="task_view_more d-flex align-items-center justify-content-center delete-table-row" href="javascript:;" data-id="{{ $setting->id }}" data-type="{{$type}}">
                            <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                        </a>
                    </div>
                </td>
            </tr>

            <tr class="">
                <td colspan="4" class="pt-3">
                    <a class="f-14 f-w-500 addFeature text-primary" href="javascript:;" data-id="{{$setting->id}}" data-type="icon"><i
                        class="icons icon-plus font-weight-bold mr-1"></i>@lang('app.addNew') @lang('superadmin.menu.featureWithIcon')</a>
                </td>
            </tr>

            <tr>
                <td colspan="4" class="p-0">
                    <x-table class="mb-0 table-hover">
                        <x-slot name="thead">
                                <th>@lang('app.title')</th>
                                <th>@lang('app.description')</th>
                                <th>@lang('app.language')</th>
                                <th>{{__('superadmin.types.icon')}}</th>
                                <th class="text-right pr-20">@lang('app.action')</th>
                        </x-slot>
                        @forelse($setting->features as $feature)
                            <tr>

                                <td>{{ $feature->title }}</td>
                                <td>{!! mb_strimwidth($feature->description, 0, 30, '...')  !!}</td>
                                <td>{{ $feature->language ? $feature->language->language_name : 'English' }}</td>
                                <td><i class="{{ $feature->icon }}"></i></td>
                                <td class="text-right pr-20">
                                    <div class="task_view">
                                        <a class="task_view_more d-flex align-items-center justify-content-center edit-feature" data-id="{{$feature->id}}" data-type="icon">
                                            <i class="fa fa-edit icons mr-2"></i>  @lang('app.edit')
                                        </a>
                                    </div>
                                    <div class="task_view mt-1 mt-lg-0 mt-md-0">
                                        <a class="task_view_more d-flex align-items-center justify-content-center delete-table-row" href="javascript:;" data-setting-id="{{$setting->id}}"
                                            data-id="{{ $feature->id }}" data-type="icon">
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
                </td>
            </tr>

        </x-table>
@empty

    <x-cards.no-record icon="list" :message="__('messages.noRecordFound')" />

@endforelse
