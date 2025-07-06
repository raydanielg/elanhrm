@if($trFrontDetail->client_title ||$trFrontDetail->client_detail || $frontClients->count()>0)
    <div class="clients bg-light">
        <div class="container">
            <div class="row align-items-center">
                @if($trFrontDetail->client_title ||$trFrontDetail->client_detail)
                    <div class="col-12 mb-30 text-center">
                        <p class="c-blue mb-2">{{ $trFrontDetail->client_title }}</p>
                        <h4>{{ $trFrontDetail->client_detail }}</h4>

                    </div>
                @endif
                @if($frontClients->count()>0)
                    <div class="col-12">
                        <div class="client-slider" id="client-slider">
                            @foreach($frontClients as $frontClient)
                                <div class="client-img">
                                    <div class="img-holder">
                                        <img src="{{ $frontClient->image_url }}" alt="partner">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

