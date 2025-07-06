<x-cards.notification :notification="$notification"  :link="(user()->is_superadmin ? route('superadmin.super_admin_dashboard') : route('dashboard'))" :image="global_setting()->logo_url"
    :title="$notification->data['company_name']" :text="__('superadmin.companyApproved.text')" :time="$notification->created_at" />
