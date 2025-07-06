{{-- Debug output --}}
@props(['title', 'summary', 'completed', 'link'])

<div class="onboarding-item p-3 mb-3 rounded shadow-sm">
    <div class="d-flex align-items-center">
        <div class="flex-grow-1">
            <h5 class="mb-1 fw-bold">{!! $title !!}</h5>
            <p class="mb-0 text-muted">{!! $summary !!}</p>
        </div>
        <div class="ms-3">
            @if ($completed)
                <div class="completed-badge">
                    <i class="bi bi-check-circle-fill text-success f-20"></i>
                    <span class="ms-1 small text-success">Completed</span>
                </div>
            @else
                <x-forms.link-secondary
                    :link="$link"
                    data-redirect-url="{{ url()->full() }}"
                    class="btn-sm"
                    icon="arrow-right">
                    <span>Start</span>
                </x-forms.link-secondary>
            @endif
        </div>
    </div>
</div>