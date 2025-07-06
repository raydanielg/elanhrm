@if(!empty($featureWithImages))
<section class="section" id="section-features">
    <div class="container">
        <header class="section-header">
            <small>Features</small>
            <h2>{{ $trFrontDetail->feature_title }}</h2>
            <p class="lead">{{ $trFrontDetail->feature_description }}</p>
            <hr>
        </header>

        @forelse($featureWithImages as $key => $value)
            @if($key % 2 == 0)
                <div class="row gap-y align-items-center">
                    <div class="col-12  col-md-5 text-center">
                        <img src="{{ $value->image_url }}" alt="..." class="shadow-4">
                    </div>

                    <div class="col-12 offset-md-1  col-md-6">
                        <h5>{{ $value->title }}</h5>
                        <p>{!! $value->description !!} </p>
                    </div>
                </div>
            @else
                <div class="row gap-y align-items-center">
                    <div class="col-12 col-md-6">
                        <h5>{{ $value->title }}</h5>
                        <p>{!! $value->description !!} </p>
                    </div>

                    <div class="col-12 offset-md-1 col-md-5 text-center">
                        <img src="{{ $value->image_url }}" alt="..." class="shadow-4">
                    </div>
                </div>
            @endif
{{--        {{ (sizeof($featureWithImages)-1) .  $key}}--}}
            {{--<hr class="w-200 my-90">--}}
            @if((sizeof($featureWithImages)-1) !=  $key)<hr class="w-200 my-90"> @endif
        @empty
        @endforelse
    </div>
</section>
@endif
@if(!empty($featureWithIcons))
    <section class="section bg-gray">
        <div class="container">
            <div class="row gap-y">
                @foreach($featureWithIcons as $featureWithIcon)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="flexbox gap-items-4">
                        <div>
                            <i class="{{ $featureWithIcon->icon }} fs-25 pt-4 text-secondary"></i>
                        </div>

                        <div>
                            <h5>{{ $featureWithIcon->title }}</h5>
                            <p>{!! $featureWithIcon->description !!} </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
