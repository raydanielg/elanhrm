<x-cards.notification :notification="$notification"  :link="(user()->is_superadmin ? route('superadmin.super_admin_dashboard') : route('billing.index'))" :image="global_setting()->logo_url"
    :title="__('superadmin.licenseExpire.subject')" :text="__('superadmin.licenseExpire.text')" :time="$notification->created_at" />
