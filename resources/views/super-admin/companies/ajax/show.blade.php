@push('styles')
    <style>
        pre {
            background: rgba(0, 0, 0, .05);
            padding: 10px;
            border-radius: 5px;
        }
    </style>
@endpush
<!-- ROW START -->
<div class="row">
    @php
        $updateCompanyPackagePermission = user()->permission('update_company_package');
        $manageCompanyImpersonatePermission = user()->permission('manage_company_impersonate');
    @endphp
    @if (!$company->approved && global_setting()->company_need_approval)
        <div class="col-md-12">
            <x-alert type="danger">
                <div class="d-flex justify-content-between align-items-center f-15">
                    @lang('superadmin.companies.companyNeedApproval')

                    <x-forms.button-primary class="approve-company" data-company-id="{{ $company->id }}"
                                            icon="check-circle">
                        @lang('app.approve')
                    </x-forms.button-primary>

                </div>
            </x-alert>
        </div>
    @endif

    <!--  USER CARDS START -->
    <div class="col-md-6 col-xl-4 mb-4 mb-xl-0 mb-lg-4 mb-md-0">
        <div class="row">

            <div class="col-md-12">
                <div class="card border-0 b-shadow-4">
                    <div class="card-horizontal align-items-center">
                        <div class="card-img">
                            <img class="" src="{{ $company->logo_url }}" alt="">
                        </div>
                        <div class="card-body border-0 pl-0">

                            <div class="row">
                                <div class="col-10">
                                    <h4 class="card-title f-15 f-w-500 text-darkest-grey mb-0">
                                        {{ $company->company_name }}
                                    </h4>
                                </div>
                                <div class="col-2 text-right">
                                    <div class="dropdown">
                                        <button class="btn f-14 px-0 py-0 text-dark-grey dropdown-toggle"
                                                type="button" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                            <i class="fa fa-ellipsis-h"></i>
                                        </button>

                                        <div
                                            class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                            aria-labelledby="dropdownMenuLink" tabindex="0">
                                            <a class="dropdown-item openRightModal"
                                               href="{{ route('superadmin.companies.edit', $company->id) }}">@lang('app.edit')</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="card-text f-11 text-lightest mb-0">@lang('app.createdOn') :
                                {{ $company->created_at->timezone(global_setting()->timezone)->translatedFormat(global_setting()->date_format . ' ' . global_setting()->time_format) }}
                            </p>
                            <p class="card-text f-11 text-lightest">@lang('app.lastLogin')

                                @if (!is_null($company->last_login))
                                    {{ $company->last_login->timezone(global_setting()->timezone)->translatedFormat(global_setting()->date_format . ' ' . global_setting()->time_format) }}
                                @else
                                    --
                                @endif
                            </p>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
    <!--  USER CARDS END -->

    <!--  USER CARDS START -->
    <div class="col-md-6 col-xl-4 mb-4 mb-xl-0 mb-lg-4 mb-md-0">
        @if($company->user)
            <x-cards.user :image="$company->user->image_url">
                <div class="row mb-1">
                    <div class="col-12">
                        <h4 class="card-title f-15 f-w-500 text-darkest-grey mb-0">
                            {{ ($company->user->salutation ? $company->user->salutation->label() . ' ' : '') . $company->user->name }}
                            @if(global_setting()->email_verification)
                                @if(is_null($company->user->userAuth->email_verified_at))
                                    <i class="fa fa-times-circle text-red" data-toggle="tooltip"
                                       data-original-title="@lang('superadmin.notVerifiedEmail')"></i>
                                @else
                                    <i class="fa fa-check-circle text-success" data-toggle="tooltip"
                                       data-original-title="@lang('superadmin.verifiedEmail')"></i>
                                @endif
                            @endif
                        </h4>
                    </div>
                </div>
                @if ($company->user->country)
                    <p class="f-12 font-weight-normal text-dark-grey mb-1">
                        <span
                            class='flag-icon flag-icon-{{ $company->user->country->iso }} flag-icon-squared'></span> {{ $company->user->country->nicename }}
                    </p>
                @endif

                <p class="card-text f-12 text-lightest">@lang('app.lastLogin')

                    @if (!is_null($company->user->last_login))
                        {{ $company->user->last_login->timezone(global_setting()->timezone)->translatedFormat(global_setting()->date_format . ' ' . global_setting()->time_format) }}
                    @else
                        --
                    @endif
                </p>
            </x-cards.user>
        @else
            <x-cards.user :image="'https://www.gravatar.com/avatar/noimage.png?s=200&d=mp'">
                <div class="card-text f-12 text-lightest m-t-5">There is no active company admin for this company</div>
            </x-cards.user>
        @endif
    </div>
    <!--  USER CARDS END -->

    <!--  WIDGETS START -->
    <div class="col-xl-4 col-md-12">
        <x-cards.data>
            <div class="row">
                <div class="col-12">
                    <h4 class="card-title f-15 f-w-500 text-darkest-grey mb-1">
                        @lang('superadmin.package'): {{ $company->package->name }}
                    </h4>
                </div>
            </div>
            @if ($company->package->package != 'lifetime')
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="card-text f-11 text-lightest mb-0">
                                    @lang('superadmin.licenceExpiresOn'):
                                    @if (!is_null($company->licence_expire_on))
                                        <span class="font-weight-bold">
                                            {{ \Carbon\Carbon::parse($company->licence_expire_on)->timezone(global_setting()->timezone)->translatedFormat(global_setting()->date_format) }}
                                        </span>
                                    @else
                                        --
                                    @endif
                                </p>
                                <p class="card-text f-11 text-lightest mb-0">
                                    @lang('superadmin.packageType'):
                                    <span class="font-weight-bold">{{ __('superadmin.' . $company->package_type) ?? '--' }}</span>
                                </p>
                                <p class="card-text f-11 text-lightest mb-0">
                                    @lang('app.amount'):
                                    <span class="font-weight-bold">{{ $currency?->currency_symbol . $latestInvoice?->total ?? '--' }}</span>
                                </p>
                            </div>
                            <div>
                                <p class="card-text f-11 text-lightest mb-0">
                                    @lang('superadmin.paymentDate'):
                                    <span class="font-weight-bold">{{ $latestInvoice?->pay_date?->format($global->date_format) ?? '--' }}</span>
                                </p>
                                <p class="card-text f-11 text-lightest mb-0">
                                    @lang('superadmin.nextPaymentDate'):
                                    <span class="font-weight-bold">{{ $latestInvoice?->next_pay_date?->format($global->date_format) ?? '--' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="d-flex justify-content-between mt-3">
                @if($updateCompanyPackagePermission == 'all')
                    <a href="{{ route('superadmin.companies.edit_package', [$company->id]) }}?requestFrom=show"
                       class="btn btn-primary rounded f-12 px-2 py-1 openRightModal">
                        <i class="fa fa-edit mr-1"></i> @lang('app.update') @lang('superadmin.package')
                    </a>
                @endif
                @if($manageCompanyImpersonatePermission == 'all')
                    <button type="button" id="login-as-company"
                            class="btn btn-outline-secondary rounded f-12 px-2 py-1">
                        <i class="fa fa-sign-in-alt mr-1"></i> @lang('superadmin.superadmin.loginAsCompany')
                    </button>
                @endif
            </div>
        </x-cards.data>
    </div>
    <!--  WIDGETS END -->
</div>
<!-- ROW END -->

<!-- ROW START -->
<div class="row mt-4">
    <div class="col-xl-8 col-lg-7 col-md-6 mb-4 mb-xl-0 mb-lg-4">
        <x-cards.data :title="__('modules.client.companyDetails')">
            <x-cards.data-row :label="__('modules.accountSettings.companyEmail')" :value="$company->company_email"/>
            <x-cards.data-row :label="__('modules.accountSettings.companyPhone')"
                              :value="$company->company_phone ?? '--'"/>

            <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                <p class="mb-0 text-lightest f-14 w-30 text-capitalize">{{ __('modules.accountSettings.companyWebsite') }}</p>
                <p class="mb-0 text-dark-grey f-14 w-70 text-wrap">
                    <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a>
                </p>
            </div>

            <x-cards.data-row :label="__('modules.accountSettings.companyAddress')"
                              :value="isset($company->defaultAddress) ? $company->defaultAddress->address : '--'"
                              html="true"/>
            <x-cards.data-row :label="__('modules.accountSettings.defaultCurrency')"
                              :value="$company->currency->currency_code . ' (' . $company->currency->currency_symbol . ')'"/>
            <x-cards.data-row :label="__('modules.accountSettings.defaultTimezone')" :value="$company->timezone"/>

            @if (module_enabled('Subdomain'))

                <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                    <p class="mb-0 text-lightest f-14 w-30 text-capitalize">Subdomain</p>
                    @if($company->sub_domain)
                        <div class="mb-0 text-dark-grey f-14 w-70 text-wrap  p-0"><a
                                href="http://{{ $company->sub_domain }}" class="text-dark-grey"
                                target="_blank">{{ $company->sub_domain }}</a></div>
                    @else
                        <p class="mb-0 f-14 text-red">
                            {{__('superadmin.subdomainNotAdded')}}
                        </p>
                    @endif

                </div>

            @endif

            <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                <p class="mb-0 text-lightest f-14 w-30 d-inline-block text-capitalize">
                    @lang('app.status')</p>
                <p class="mb-0 text-dark-grey f-14 w-70">
                    @if ($company->status == 'active')
                        <i class="fa fa-circle mr-1 text-dark-green f-10"></i>
                    @else
                        <i class="fa fa-circle mr-1 text-red f-10"></i>
                    @endif
                    {{ __('app.'.$company->status) }}
                </p>
            </div>

            @if (global_setting()->company_need_approval)
                <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block text-capitalize">
                        @lang('app.approved')</p>
                    <p class="mb-0 text-dark-grey f-14 w-70">
                        @if ($company->approved == 1)
                            <i class="fa fa-circle mr-1 text-dark-green f-10"></i> @lang('app.yes')
                        @else
                            <i class="fa fa-circle mr-1 text-red f-10"></i> @lang('app.no')
                        @endif
                    </p>
                </div>
            @endif

            @if (!is_null($company->approved_by))
                <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block text-capitalize">
                        @lang('superadmin.companies.approvedChangedBy')</p>

                    <div class="media align-items-center mw-250">
                        <img src="{{ $company->approvalBy->image_url }}" class="mr-2 taskEmployeeImg rounded-circle"
                             alt="{{ $company->approvalBy->name }}"
                             title="{{ $company->approvalBy->userBadge() }}">

                        <div class="media-body {{$company->approvalBy->status}}">

                            <h5 class="mb-0 f-12">
                                {!! $company->approvalBy->userBadge() !!}
                            </h5>
                        </div>
                    </div>

                </div>
            @endif

        </x-cards.data>
    </div>

    <div class="col-xl-4 col-lg-7 col-md-6 mb-4 mb-xl-0 mb-lg-4">
        @php
            $storage = __('superadmin.notUsed');
            if ($company->file_storage_count && $company->file_storage_sum_size) {
                if ($company->package->storage_unit == 'mb') {
                    $storage = \App\Models\SuperAdmin\Package::bytesToMB($company->file_storage_sum_size) . ' ' . __('superadmin.mb');
                } else {
                    $storage = \App\Models\SuperAdmin\Package::bytesToGB($company->file_storage_sum_size)  . ' ' . __('superadmin.gb');
                }
            }

            $maxStorage = __('superadmin.unlimited');
            if ($company->package->max_storage_size != -1) {
                $maxStorage = $company->package->max_storage_size . ' ' . strtoupper($company->package->storage_unit);
            }
        @endphp
        <x-cards.data :title="__('app.statistics')" padding="false">
            <x-table class="table-hover">
                <tr>
                    <td class="pl-20">
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people mr-2 text-primary" viewBox="0 0 16 16">
                                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8Zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816ZM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0Zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/>
                            </svg>
                            @lang('app.menu.employees')
                        </div>
                    </td>
                    <td class="text-right pr-20 {{ $company->employees_count >= $company->package->max_employees ? 'text-danger font-weight-bold' : '' }}">
                        {{ $company->employees_count . ' / ' . $company->package->max_employees }}
                    </td>
                </tr>
                <tr>
                    <td class="pl-20">
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hdd-stack mr-2 text-info" viewBox="0 0 16 16">
                                <path d="M14 10a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-1a1 1 0 0 1 1-1h12zM2 9a2 2 0 0 0-2 2v1a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-1a2 2 0 0 0-2-2H2z"/>
                                <path d="M5 11.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zm-2 0a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zM14 3a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h12zM2 2a2 2 0 0 0-2 2v1a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H2z"/>
                                <path d="M5 4.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zm-2 0a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                            </svg>
                            @lang('superadmin.storage')
                        </div>
                    </td>
                    <td class="text-right pr-20">{{ $storage . ' / ' . $maxStorage }}</td>
                </tr>
                <tr>
                    <td class="pl-20">
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-badge mr-2 text-success" viewBox="0 0 16 16">
                                <path d="M6.5 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1h-3zM11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                <path d="M4.5 0A2.5 2.5 0 0 0 2 2.5V14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2.5A2.5 2.5 0 0 0 11.5 0h-7zM3 2.5A1.5 1.5 0 0 1 4.5 1h7A1.5 1.5 0 0 1 13 2.5v10.795a4.2 4.2 0 0 0-.776-.492C11.392 12.387 10.063 12 8 12s-3.392.387-4.224.803a4.2 4.2 0 0 0-.776.492V2.5z"/>
                            </svg>
                            @lang('app.menu.clients')
                        </div>
                    </td>
                    <td class="text-right pr-20">{{ $company->clients_count }}</td>
                </tr>
                <tr>
                    <td class="pl-20">
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-receipt mr-2 text-warning" viewBox="0 0 16 16">
                                <path d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27zm.217 1.338L2 2.118v11.764l.137.274.51-.51a.5.5 0 0 1 .707 0l.646.647.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.509.509.137-.274V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0L4 1.707l-.646.647a.5.5 0 0 1-.708 0l-.509-.51z"/>
                                <path d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm8-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5z"/>
                            </svg>
                            @lang('app.menu.invoices')
                        </div>
                    </td>
                    <td class="text-right pr-20">{{ $company->invoices_count }}</td>
                </tr>
                <tr>
                    <td class="pl-20">
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-text mr-2 text-secondary" viewBox="0 0 16 16">
                                <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
                                <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                            </svg>
                            @lang('app.menu.estimates')
                        </div>
                    </td>
                    <td class="text-right pr-20">{{ $company->estimates_count }}</td>
                </tr>
                <tr>
                    <td class="pl-20">
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-kanban mr-2 text-primary" viewBox="0 0 16 16">
                                <path d="M13.5 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-11a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h11zm-11-1a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2h-11z"/>
                                <path d="M6.5 3a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1V3zm-4 0a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1V3zm8 0a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1V3z"/>
                            </svg>
                            @lang('app.menu.projects')
                        </div>
                    </td>
                    <td class="text-right pr-20">{{ $company->projects_count }}</td>
                </tr>
                <tr>
                    <td class="pl-20">
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-square mr-2 text-success" viewBox="0 0 16 16">
                                <path d="M3 14.5A1.5 1.5 0 0 1 1.5 13V3A1.5 1.5 0 0 1 3 1.5h8a.5.5 0 0 1 0 1H3a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5V8a.5.5 0 0 1 1 0v5a1.5 1.5 0 0 1-1.5 1.5H3z"/>
                                <path d="m8.354 10.354 7-7a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/>
                            </svg>
                            @lang('app.menu.tasks')
                        </div>
                    </td>
                    <td class="text-right pr-20">{{ $company->tasks_count }}</td>
                </tr>
                <tr>
                    <td class="pl-20">
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-lines-fill mr-2 text-warning" viewBox="0 0 16 16">
                                <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-5 6s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zM11 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5zm.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1h-4zm2 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1h-2zm0 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1h-2z"/>
                            </svg>
                            @lang('app.menu.leads')
                        </div>
                    </td>
                    <td class="text-right pr-20">{{ $company->leads_count }}</td>
                </tr>
                <tr>
                    <td class="pl-20">
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart3 mr-2 text-info" viewBox="0 0 16 16">
                                <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                            </svg>
                            @lang('app.menu.orders')
                        </div>
                    </td>
                    <td class="text-right pr-20">{{ $company->orders_count }}</td>
                </tr>
                <tr>
                    <td class="pl-20">
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-ticket-perforated mr-2 text-danger" viewBox="0 0 16 16">
                                <path d="M4 4.85v.9h1v-.9H4Zm7 0v.9h1v-.9h-1Zm-7 1.8v.9h1v-.9H4Zm7 0v.9h1v-.9h-1Zm-7 1.8v.9h1v-.9H4Zm7 0v.9h1v-.9h-1Zm-7 1.8v.9h1v-.9H4Zm7 0v.9h1v-.9h-1Z"/>
                                <path d="M1.5 3A1.5 1.5 0 0 0 0 4.5V6a.5.5 0 0 0 .5.5 1.5 1.5 0 1 1 0 3 .5.5 0 0 0-.5.5v1.5A1.5 1.5 0 0 0 1.5 13h13a1.5 1.5 0 0 0 1.5-1.5V10a.5.5 0 0 0-.5-.5 1.5 1.5 0 0 1 0-3A.5.5 0 0 0 16 6V4.5A1.5 1.5 0 0 0 14.5 3h-13ZM1 4.5a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 .5.5v1.05a2.5 2.5 0 0 0 0 4.9v1.05a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-1.05a2.5 2.5 0 0 0 0-4.9V4.5Z"/>
                            </svg>
                            @lang('app.menu.tickets')
                        </div>
                    </td>
                    <td class="text-right pr-20">{{ $company->tickets_count }}</td>
                </tr>
                <tr>
                    <td class="pl-20">
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-text mr-2 text-secondary" viewBox="0 0 16 16">
                                <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
                                <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                            </svg>
                            @lang('app.menu.contracts')
                        </div>
                    </td>
                    <td class="text-right pr-20">{{ $company->contracts_count }}</td>
                </tr>
            </x-table>
        </x-cards.data>
    </div>
</div>

<!-- ROW END -->


<script>
    $('body').on('click', '#login-as-company', function () {
        Swal.fire({
            title: `@lang('messages.sweetAlertTitle')`,
            text: `@lang('superadmin.loginInfo')`,
            icon: 'warning',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: `@lang('app.login')`,
            cancelButtonText: `@lang('app.cancel')`,
            customClass: {
                confirmButton: 'btn btn-primary mr-3',
                cancelButton: 'btn btn-secondary'
            },
            showClass: {
                popup: 'swal2-noanimation',
                backdrop: 'swal2-noanimation'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                const url = "{{ route('superadmin.companies.login_as_company', $company->id) }}";

                const token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    blockUI: true,
                    data: {
                        '_token': token
                    },
                    success: function (response) {
                        if (response.status == "success") {
                            location.href = "{{ route('dashboard') }}"
                        }
                    }
                });
            }
        });
    });

    $('body').on('click', '.approve-company', function () {
        var companyId = $(this).data('company-id');

        Swal.fire({
            title: `@lang('messages.sweetAlertTitle')`,
            icon: 'warning',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: `@lang('app.approve')`,
            cancelButtonText: `@lang('app.cancel')`,
            customClass: {
                confirmButton: 'btn btn-primary mr-3',
                cancelButton: 'btn btn-secondary'
            },
            showClass: {
                popup: 'swal2-noanimation',
                backdrop: 'swal2-noanimation'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                var url = "{{ route('superadmin.companies.approve_company') }}";

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    blockUI: true,
                    data: {
                        '_token': token,
                        'companyId': companyId
                    },
                    success: function (response) {
                        if (response.status == "success") {
                            window.location.reload();
                        }
                    }
                });
            }
        });
    });
</script>
