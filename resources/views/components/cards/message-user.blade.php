@php
$user = $message->from != user()->id ? $message->fromUser : $message->toUser;
$unreadMessageCount = $user->unread_messages_count ?? 0;
@endphp
{{-- @if ($message->toUser->unread_messages_count > 0)
    @php
        $unreadMessageCount = $message->toUser->unread_messages_count;
    @endphp
@else
    @php
        $unreadMessageCount = $message->fromUser->unread_messages_count;
    @endphp
@endif --}}
<style>
    .message-container {
        display: -webkit-box;
        -webkit-line-clamp: 2; /* Limit to 2 lines */
        -webkit-box-orient: vertical;
        overflow: hidden;
        max-height: 4em; /* Adjust based on font-size */
    }
</style>

<div class="card rounded-0 border-top-0 border-left-0 border-right-0 user_list_box position-relative" id="user-no-{{ $user->id }}">
    <a @class([
        'tablinks',
        'show-user-messages',
        'unread-message' => $unreadMessageCount > 0,
    ]) href="javascript:;" data-name="{{ $user->name }}"
        data-user-id="{{ $user->id }}"
        data-unread-message-count="{{ $unreadMessageCount }}"
        style="display: block; width: 100%; text-decoration: none; color: inherit;">
        <div class="card-horizontal user-message d-flex align-items-center">
            <div class="card-img">
                <img class="" src="{{ $user->image_url }}" alt="{{ $user->name }}">
            </div>
            <div class="card-body border-0 pl-0 w-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="pt-3">
                        <h4 class="card-title f-12 f-w-500 text-dark-grey mb-0">{{ $user->name }}</h4>
                        <p class="card-date f-11 text-dark-grey mb-0 pt-1">
                            {{ \Carbon\Carbon::parse($message->created_at)->diffForHumans() }}
                        </p>
                    </div>
                </div>

                <div @class([
                    'card-text',
                    'f-11',
                    'text-lightest',
                    'd-flex',
                    'justify-content-between',
                    'message-mention',
                    'text-dark' => $unreadMessageCount > 0,
                    'font-weight-bold' => $unreadMessageCount > 0,
                ])>
                <div class="message-container">{!! nl2br($message->message) !!}</div>

                    @if ($unreadMessageCount > 0)
                        <div>
                            <span class="badge badge-primary ml-1 unread-count">{{ $unreadMessageCount }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </a>

    <!-- Three Dot Menu (Placed outside the <a> tag) -->
    @if ($user->id == user()->id || in_array('admin', user_roles()))
        <div class="dropdown position-absolute" style="top: 10px; right: 10px; padding-top: 25px;">
            <a href="javascript:;" class="text-dark-grey" id="dropdownMenuButton-{{ $user->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-ellipsis-v"></i> <!-- Three dots icon -->
            </a>
            <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0" aria-labelledby="dropdownMenuButton-{{ $user->id }}" tabindex="0" >
                <a class="dropdown-item delete-all-message" data-user-id="{{ $user->id }}" href="javascript:;">@lang('app.delete')</a>
            </div>
        </div>
    @endif
</div><!-- card end -->

