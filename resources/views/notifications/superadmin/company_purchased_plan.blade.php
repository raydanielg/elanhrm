<x-cards.notification :notification="$notification"  :link="(user()->is_superadmin ? route('superadmin.super_admin_dashboard') : route('dashboard'))" :image="global_setting()->logo_url"
    :title="__('superadmin.planPurchase.subject')" :text="$notification->data['company_name']. ' ' . __('superadmin.planPurchase.text') . ' ' . $notification->data['name']" :time="$notification->created_at" />
