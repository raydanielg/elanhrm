@extends('super-admin.layouts.saas-app')
@section('header-section')
    @include('super-admin.saas.section.breadcrumb')
@endsection

@section('content')
    <style>
        .video-box {
            width:75%;
            height: 400px;
        }
    </style>
    <section class="pricing-section bg-white sp-100-40">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p>
                        @if(!is_null($slugData->video_embed))
                            <div class="row">
                                {!! $slugData->video_embed !!}
                            </div>
                        @elseif(!is_null($slugData->video_link))
                            <div class="row">
                                <div align="center" class="embed-responsive embed-responsive-4by3 video-box">
                                    <video controls class="embed-responsive-item">
                                        <source src="{!! $slugData->video_link !!}" type="video/mp4">
                                    </video>

                                </div>
                            </div>
                        @elseif(!is_null($slugData->file_name))
                            <div class="row">
                                <div align="center" class="embed-responsive embed-responsive-4by3 video-box">
                                    <video controls class="embed-responsive-item">
                                        <source src="{{ asset_url('footer-files/'.$slugData->id.'/'.$slugData->file_name) }}" type="video/mp4">
                                    </video>

                                </div>
                            </div>
                        @endif
                    </p>
                </div>
                <span class="ql-editor">{!! nl2br($slugData->description) !!}</span>
            </div>
        </div>
    </section>

@endsection
@push('footer-script')

@endpush
