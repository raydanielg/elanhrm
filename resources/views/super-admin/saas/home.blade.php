@extends('super-admin.layouts.saas-app')

@section('content')

    @include('super-admin.saas.section.header')

    @include('super-admin.saas.section.client')

    @include('super-admin.saas.section.feature')

    @include('super-admin.saas.section.testimonial')

@endsection
@push('footer-script')
    <script>
        $(document).ready(function () {
            const maxHeight = Math.max(...$('.planNameHead').map(function () {
                return $(this).height();
            }));

            $('.planNameHead').height(Math.round(maxHeight)).next('.planNameTitle').height(Math.round(maxHeight - 28));
        });

        function planShow(type) {
            $('#monthlyPlan').toggle(type === 'monthly');
            $('#annualPlan').toggle(type !== 'monthly');
        }

    </script>

@endpush
