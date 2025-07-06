<div
    {{ $attributes->merge(['class' => 'bg-white p-20 rounded border d-flex justify-content-between align-items-center']) }}>
    <div class="d-block ">
        <h5 class="f-13 f-w-500 text-darkest-grey">{{ $title }}
            @if (!is_null($info))
            <i class="fa fa-question-circle" data-toggle="popover" data-placement="top" data-content="{{ $info }}" data-html="true" data-trigger="hover"></i>
        @endif
        </h5>
        <div class="d-flex">
            <p class="mb-0 f-13 font-weight-bold text-blue text-primary d-grid"><span
                    id="{{ $widgetId }}">{{ $value }}</span>
            </p>
        </div>
    </div>
    <div class="d-block">
        <i class="fa fa-{{ $icon }} text-lightest f-18"></i>
    </div>
</div>
