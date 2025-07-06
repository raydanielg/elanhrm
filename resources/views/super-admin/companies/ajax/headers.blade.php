@push('styles')
    <style>
        .stats-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .stats-header {
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
        }
        .stats-body {
            padding: 20px;
        }
        .table-stats {
            margin-bottom: 0;
        }
        .table-stats td {
            padding: 12px 15px;
            vertical-align: middle;
        }
        .table-stats span {
            color: #495057;
            font-weight: 500;
            border-bottom: 1px dotted #000;
            text-decoration: none;
        }
        .stats-label {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
        .stats-value {
            font-weight: 600;
            color: #2c3e50;
        }
        .flag-icon {
            margin-right: 8px;
        }
        .status-icon {
            font-size: 1.1rem;
        }
        .location-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
        }
    </style>
@endpush

<div class="row">
    <div class="col-md-12">
        <div class="stats-card">
            <div class="stats-header">
                <h4 class="mb-0">{{__('app.statistics')}}</h4>
            </div>
            <div class="stats-body">
                <div class="stats-label">
                    {{__('superadmin.browserDetectDescription')}}
                </div>

                <div class="row">
                    <div @class(['col-md-7' => $company->location_details, 'col-md-12' => !$company->location_details])>
                        <x-table class="table-stats">
                            <x-slot name="thead">
                                <th width="40%">{{__('app.type')}}</th>
                                <th>{{__('app.value')}}</th>
                            </x-slot>
                            @if($company->headers)
                                @foreach(json_decode($company->headers,true) as $index=>$head)
                                    <tr>
                                        <td>
                                            <span data-toggle="tooltip"
                                                  data-original-title="{{__('superadmin.browserDetectTooltip.'.$index)}}">
                                                @if(is_bool($head))
                                                    {{$index}}
                                                @else
                                                    {{ ucwords(preg_replace('/(?<!\ )[A-Z]/', ' $0', $index))}}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="stats-value">
                                            @if(is_bool($head))
                                                @if($head)
                                                    <i class="fa fa-check-circle text-success status-icon" data-toggle="tooltip" title="{{__('app.yes')}}"></i>
                                                @else
                                                    <i class="fa fa-times text-danger status-icon" data-toggle="tooltip" title="{{__('app.no')}}"></i>
                                                @endif
                                            @else
                                                {{ $head ?: '-' }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td>{{__('superadmin.registeredIp')}}</td>
                                    <td class="stats-value">{{ trim($company->register_ip) ?? '-'}}</td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="2">
                                        <x-cards.no-record icon="list" :message="__('messages.noRecordFound')"/>
                                    </td>
                                </tr>
                            @endif
                        </x-table>
                    </div>

                    @if($company->location_details)
                        <div class="col-md-5">
                            <div class="location-details">
                                <x-table class="table-stats">
                                    <x-slot name="thead">
                                        <th>{{ucwords(__('app.location'))}}</th>
                                        <th>{{__('app.details')}}</th>
                                    </x-slot>
                                    @php($details = json_decode($company->location_details,true))
                                    @foreach($details as $index => $head)
                                        @continue($index=='driver')
                                        <tr>
                                            <td>
                                                <span class="stats-label">
                                                    {{ ucwords(preg_replace('/(?<!\ )[A-Z]/', ' $0', $index))}}
                                                </span>
                                            </td>
                                            <td class="stats-value">
                                                @if($index ==='countryName')
                                                    <i class="flag-icon flag-icon-{{strtolower($details['isoCode'])}} flag-icon-squared"></i>
                                                @endif
                                                {{ $head ?: '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </x-table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ROW END -->

