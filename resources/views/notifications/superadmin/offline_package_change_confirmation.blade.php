@php
   $text = __('superadmin.offlinePackageRequestChange.text', ['status' => __('superadmin.offlineRequestStatus.' . $notification->data['status']), 'package' => $notification->data['package_name']]);


    if($notification->data['status'] == 'rejected'){
         $text .= '<br>'.__('app.remark') . ': ' . $notification->data['remark'];
    }

@endphp
<x-cards.notification :notification="$notification"  :link="route('billing.index', ['tab'=>'offline-request'])" :image="global_setting()->logo_url"
    :title="__('superadmin.offlinePackageRequestChange.subject')" :text="$text" :time="$notification->created_at" />
