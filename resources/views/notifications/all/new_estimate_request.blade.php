@php
    use App\Models\User;
    use App\Models\EstimateRequest;

    $estimateRequest = EstimateRequest::where('id', $notification->data['id'])->first();

    if(isset($estimateRequest)) {
        $notificationUser = User::find($estimateRequest->added_by);
    }
@endphp

@if ($notificationUser)
    <x-cards.notification :notification="$notification" :link="route('estimate-request.show', $notification->data['id'])"
                          :image="$notificationUser->image_url"
                          :title="__('email.estimate_request_invite.subject')" :text="$notification->data['estimate_request_number']"
                          :time="$notification->created_at"/>
@endif
