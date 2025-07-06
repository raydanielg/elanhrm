<x-cards.data :title="__('superadmin.dashboard.recentLicenceExpiredCompanies')"
              padding="false"
              otherClasses="h-200">
    <div class="table-responsive">

        <x-table class="border-0 mb-0 admin-dash-table table-hover">

            <x-slot name="thead">
                <th class="pl-20">#</th>
                <th>@lang('app.name')</th>
                <th>@lang('superadmin.packages.packages')</th>
                <th>@lang('app.date')</th>
            </x-slot>

            @forelse($recentLicenceExpiredCompanies as $key=>$item)
                <tr id="row-{{ $item->id }}">
                    <td class="pl-20">{{ $key + 1 }}</td>
                    <td>
                        <x-company :company="$item" />
                    </td>
                    <td>
                        {{ ($item->package ? $item->package->name : '--') . ' (' . $item->package_type . ')' }}
                    </td>
                    <td>
                        {{ $item->created_at->translatedFormat(global_setting()->date_format) }}
                    </td>
                </tr>
            @empty
                <x-cards.no-record-found-list colspan="5"/>
            @endforelse
        </x-table>
    </div>
</x-cards.data>
