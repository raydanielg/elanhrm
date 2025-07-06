<div class="card-header bg-white  d-flex justify-content-between align-items-center">
    <h4 class="f-16 f-w-500 mb-0">{{ $slot }}</h4>

    @if($action)
        {!! $action !!}
    @endif

</div>
