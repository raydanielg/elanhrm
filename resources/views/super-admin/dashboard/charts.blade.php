<x-cards.data :title="__('superadmin.dashboard.registrationsChart')">
    <x-slot name="action">
        <x-forms.select fieldName="registration_year" fieldId="registration_year">
            @foreach(range(0, 2) as $i)
                @php($year = now(global_setting()->timezone)->subYears($i)->year)
                <option @selected(request()->year == $year) value='{{ $year }}'>{{ $year }}</option>
            @endforeach
        </x-forms.select>
    </x-slot>
    <x-bar-chart id="task-chart2" :chartData="$registrationsChart" height="300" :spaceRatio="0.5"></x-bar-chart>
</x-cards.data>
