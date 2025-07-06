@php
    $manageOfflineAcceptPermission = user()->permission('accept_reject_request');
@endphp
<div class="row" id="offlineRequestShow">
    <div class="col-sm-12">
        <div class="card bg-white border-0 b-shadow-4">
            <div class="card-header bg-white  border-bottom-grey text-capitalize justify-content-between p-20">
                <div class="row">
                    <div class="col-lg-10 col-10">
                        <h3 class="heading-h1 mb-3">@lang('superadmin.menu.offlineRequest')</h3>
                    </div>
                    <div class="col-lg-2 col-2 text-right">

                            <div class="dropdown">
                                <button
                                    class="btn btn-lg f-14 px-2 py-1 text-dark-grey text-capitalize rounded  dropdown-toggle"
                                    type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>

                                <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                    aria-labelledby="dropdownMenuLink" tabindex="0">
                                    <a class="dropdown-item" href="{{ route('superadmin.offline-plan.download', md5($offlinePlanChange->id)) }}" title="@lang('app.download') @lang('app.receipt')">
                                        <i class="fa fa-download mr-2"></i>@lang('app.download') @lang('app.receipt')
                                    </a>


                                    @if ($offlinePlanChange->status == 'pending' && $manageOfflineAcceptPermission == 'all')
                                        <a href="javascript:;" data-id="{{$offlinePlanChange->id}}" data-status="verified" class="dropdown-item statusChange">
                                            <i class="fa fa-check mr-2"></i>@lang('superadmin.offlineRequestStatusButton.verified')</a>

                                        <a href="javascript:;" data-id="{{$offlinePlanChange->id}}" data-status="rejected" class="dropdown-item statusChange">
                                            <i class="fa fa-times mr-2"></i>@lang('superadmin.offlineRequestStatusButton.rejected')</a>
                                    @endif
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            <div class="card-body">

                @php
                    switch ($offlinePlanChange->status) {
                    case 'verified':
                        $status = 'light-green';
                        break;
                    case 'rejected':
                        $status = 'red';
                        break;
                    default:
                        $status = 'yellow';
                        break;
                    }
                    $status = '<i class="fa fa-circle mr-1 text-'. $status .' f-10"></i>' . __('superadmin.offlineRequestStatus.'.$offlinePlanChange->status);

                    $download = '';
                @endphp

                <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                    <p class="mb-0 text-lightest f-14 w-30 text-capitalize">{{ __('superadmin.company') }}</p>
                    <div class="mb-0 text-dark-grey f-14 w-70 text-wrap">
                        <x-company :company="$offlinePlanChange->company" />
                    </div>
                </div>
                <x-cards.data-row :label="__('superadmin.package')" :value="$offlinePlanChange->package->name . ($offlinePlanChange->package->package == 'lifetime' ? '' : ' (' . ($offlinePlanChange->package_type == 'annual' ? __('app.annually') : __('app.monthly')) . ')')" />
                <x-cards.data-row :label="__('app.amount')" :value="global_currency_format($offlinePlanChange->amount,$offlinePlanChange->package->currency_id)" />
                <x-cards.data-row :label="__('superadmin.paymentBy')" :value="$offlinePlanChange->offlineMethod->name" />
                <x-cards.data-row :label="__('app.status')" :value="$status" />
                <x-cards.data-row :label="__('app.description')" :value="$offlinePlanChange->description" />
                <x-cards.data-row :label="__('app.createdOn')" :value="$offlinePlanChange->created_at->translatedFormat(global_setting()->date_format)" />
                <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                    <p class="mb-0 text-lightest f-14 w-30 text-capitalize">{{ __('app.receipt') }}</p>
                    <div class="mb-0 text-dark-grey f-14 w-70 text-wrap">
                        <a href="{{ route('superadmin.offline-plan.download', md5($offlinePlanChange->id)) }}" class="btn-secondary rounded f-11 py-2 px-2">
                            <i class="fa fa-download"></i>
                            {{ __('app.download') }}
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
    $('body').on('click', '.statusChange', function() {
            var planId = $(this).data('id');
            var status = $(this).data('status');
            var url = "{{ route('superadmin.offline-plan.confirmChangePlan', [':id', ':status']) }}";
            url = url.replace(':status', status);
            url = url.replace(':id', planId);
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
</script>
