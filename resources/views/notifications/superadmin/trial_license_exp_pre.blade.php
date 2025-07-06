<x-cards.notification :notification="$notification"  :link="(user()->is_superadmin ? route('superadmin.super_admin_dashboard') : route('billing.index'))" :image="global_setting()->logo_url"
    :title="__('superadmin.trialLicenseExpPre.subject')" :text="__('superadmin.trialLicenseExpPre.text')" :time="$notification->created_at" />
