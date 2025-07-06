<x-cards.data :title="__('superadmin.dashboard.packageCompanyCount')" padding="false"
              otherClasses="h-200">
    <div class="table-responsive">
        <x-table class="border-0 mb-0 admin-dash-table table-hover">

            <x-slot name="thead">
                <th class="pl-20">#</th>
                <th>@lang('app.name')</th>
                <th>@lang('superadmin.dashboard.totalCompany')</th>
            </x-slot>

            @forelse($packageCompanyCount as $key=>$item)
                <tr id="row-{{ $item->id }}" class="f-10">
                    <td class="pl-20">{{ $loop->iteration }}</td>
                    <td>
                        {{ $item->name }}
                    </td>
                    <td>
                        {{ $item->companies_count }}
                    </td>
                </tr>
            @empty
                <x-cards.no-record-found-list colspan="3" />
            @endforelse
        </x-table>
    </div>
</x-cards.data>
