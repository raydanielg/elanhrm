@extends('layouts.app')

@push('styles')
<style>
    input[type=radio].form-check-input  {
        height: 15px;
    }
</style>
@endpush

@section('content')

    <div class="content-wrapper">
        @include($view)
    </div>

@endsection
