@if(\Illuminate\Support\Facades\Route::currentRouteName() != 'front.home' && \Illuminate\Support\Facades\Route::currentRouteName() != 'front.get-email-verification')
    <section class="breadcrumb-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h2 class="text-uppercase mb-4">{{ $pageTitle }}</h2>
                    <ul class="breadcrumb mb-0 justify-content-center">
                        <li class="breadcrumb-item"><a href="/"> @lang('app.menu.home')</a></li>
                        <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endif
