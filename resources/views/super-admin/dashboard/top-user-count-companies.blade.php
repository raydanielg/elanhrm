<x-cards.data :title="__('superadmin.dashboard.companiesWithMostUsers')"
              padding="false"
              otherClasses="h-200">
    <div class="table-responsive">

        <x-table class="border-0 mb-0 admin-dash-table table-hover">

            <x-slot name="thead">
                <th class="pl-20">#</th>
                <th>@lang('app.company_name')</th>
                <th class="text-center">@lang('superadmin.superadmin.totalUsers')</th>
                <th class="text-center">@lang('app.menu.employees')</th>
                <th class="text-center pr-20">@lang('app.menu.clients')</th>
            </x-slot>

            @forelse($topCompaniesUserCount as $key=>$item)
                <tr id="row-{{ $item->id }}">
                    <td class="pl-20">{{ $key + 1 }}</td>
                    <td>
                        <x-company :company="$item" />
                    </td>
                    <td class="text-center">
                        {{ $item->users_count }}
                    </td>
                    <td class="text-center">
                        {{ $item->employees_count }}
                    </td>
                    <td class="text-center pr-20">
                        {{ $item->clients_count }}
                    </td>
                </tr>
            @empty
                <x-cards.no-record-found-list colspan="6"/>
            @endforelse
        </x-table>
    </div>
</x-cards.data>
