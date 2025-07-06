@extends('layouts.app')

@section('content')

    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 text-center mt-4">
                <h2 class="heading-h2">@lang('app.welcome') {{ user()->name }}</h2>
                <p>@lang('modules.checklist.checklistInfo')</p>
            </div>

            <div class="col-md-12 mt-4">

                <x-cards.data title="To Do List">

                    <x-cards.onboarding-item :title="__('modules.checklist.installation')"
                                             :summary="__('modules.checklist.installationInfo')" completed="true"/>

                    <x-cards.onboarding-item :title="__('modules.checklist.accountSetup')"
                                             :summary="__('modules.checklist.accountSetupInfo')" completed="true"/>

                    @php
                        $emailSetupTitle = __('modules.checklist.emailSetup') . ' <i class="fa fa-exclamation-circle text-danger ms-1" data-toggle="tooltip" data-placement="top" title="Very Important!"></i>';
                        $emailCompleted = smtp_setting()->mail_from_email != 'from@email.com' || (smtp_setting()->verified == 1 && smtp_setting()->mail_driver != 'mail');
                    @endphp
                    <div class="email-setup-container position-relative mb-2">
                        @if(!$emailCompleted)
                            <div class="priority-badge position-absolute" style="top: -10px; right: -10px; background-color: #dc3545; color: white; padding: 3px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.2); z-index: 10;">
                                <i class="fa fa-bolt"></i> Priority
                            </div>
                            <div class="pulse-animation position-absolute" style="top: 0; left: 0; right: 0; bottom: 0; border: 2px solid #dc3545; border-radius: 6px; z-index: 1; pointer-events: none;"></div>
                        @endif
                        <x-cards.onboarding-item :title="$emailSetupTitle"
                                                :summary="__('modules.checklist.configureEmailSetting')"
                                                :completed="$emailCompleted"
                                                :link="route('notifications.index')"
                                                class="email-setup-highlight"/>
                    </div>

                    <x-cards.onboarding-item :title="__('modules.checklist.crontSetup')"
                                             :summary="__('modules.checklist.cronSetupInfo')"
                                             :completed="global_setting()->last_cron_run"
                                             :link="route('app-settings.index')"/>
                    <x-cards.onboarding-item :title="__('modules.checklist.companyLogo')"
                                             :summary="__('modules.checklist.companyLogoInfo')"
                                             :completed="global_setting()->logo"
                                             :link="route('superadmin.settings.super-admin-theme-settings.index')"/>

                    <x-cards.onboarding-item :title="__('modules.checklist.favicon')"
                                             :summary="__('modules.checklist.faviconInfo')"
                                             :completed="global_setting()->favicon"
                                             :link="route('superadmin.settings.super-admin-theme-settings.index')"/>

                </x-cards.data>
            </div>
        </div>
    </div>
    <!-- CONTENT WRAPPER END -->
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Highlight the email setup item
        const emailSetupItem = document.querySelector('.email-setup-highlight');
        if (emailSetupItem) {
            emailSetupItem.closest('.onboarding-item').style.border = '2px solid #dc3545';
            emailSetupItem.closest('.onboarding-item').style.boxShadow = '0 0 10px rgba(220, 53, 69, 0.3)';
            emailSetupItem.closest('.onboarding-item').style.backgroundColor = '#fff8f8';
        }

        // Initialize tooltips
        if (typeof $().tooltip === 'function') {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
</script>
@endpush
