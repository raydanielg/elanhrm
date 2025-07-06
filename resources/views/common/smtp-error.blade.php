@if(!\App\Models\SmtpSetting::isVerified() && app()->environment('codecanyon'))
    <div class="col-lg-12 mt-2">
        <x-alert type="danger" icon="info-circle">
            It seems SMTP settings is not configured properly. Please configure it to make the emails
            works. You might get error if SMTP details are not configured
        </x-alert>
    </div>
@endif
