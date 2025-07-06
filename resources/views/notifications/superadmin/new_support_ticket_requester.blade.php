<x-cards.notification :notification="$notification"  :link="route('superadmin.support-tickets.show', $notification->data['id'])" :image="global_setting()->logo_url"
    :title="$notification->data['subject']" :text="__('superadmin.newSupportTicketRequester.text')" :time="$notification->created_at" />
