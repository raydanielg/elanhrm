<!-- START Header -->
<header class="header position-relative">
    <!-- START Navigation -->
    <div class="navigation-bar" id="affix">
        <div class="container">
            <nav class="navbar navbar-expand-lg p-0">
                <a class="logo" href="{{ route('front.home') }}">
                    <div class="d-flex align-items-center">
                        <img class="mr-2 rounded logo-default" style="max-height: 32px;" src="{{ global_setting()->logo_front_url }}" alt="Logo"/>
                    </div>
                </a>
                <button class="navbar-toggler border-0 p-0" type="button" data-toggle="collapse"
                        data-target="#theme-navbar" aria-controls="theme-navbar" aria-expanded="false"
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-lines"></span>
                </button>

                <div class="collapse navbar-collapse gap-3" id="theme-navbar">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('front.home') }}">{{ $frontMenu->home }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('front.feature') }}">{{ $frontMenu->feature }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('front.pricing') }}">{{ $frontMenu->price }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('front.contact') }}">{{ $frontMenu->contact }}</a>
                        </li>
                        @foreach ($footerSettings as $footerSetting)
                            @unless ($footerSetting->type == 'footer')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ !is_null($footerSetting->external_link) ? $footerSetting->external_link : route('front.page', $footerSetting->slug) }}">{{ $footerSetting->name }}</a>
                                </li>
                            @endif
                        @endforeach

                    </ul>
                    <div class="my-3 my-lg-0">
                        @guest
                            <a href="{{ module_enabled('Subdomain') ? route('front.workspace') : route('login') }}" class="btn btn-border shadow-none">{{ $frontMenu->login }}</a>
                            @if ($global->enable_register)
                                <a href="{{ route('front.signup.index') }}" class="btn btn-menu-signup shadow-none ml-2">{{ $frontMenu->get_start }}</a>
                            @endif
                        @else
                            <a href="{{ module_enabled('Subdomain') ? (user()->is_superadmin ? \App\Providers\RouteServiceProvider::SUPER_ADMIN_HOME : \App\Providers\RouteServiceProvider::HOME) : route('login') }}" class="btn btn-border shadow-none px-3 py-1">
                               @if(isset(user()->image_url))  <img src="{{ user()->image_url }}" class="rounded" width="25" alt="@lang('superadmin.myAccount')"> @endif @lang('superadmin.myAccount')
                            </a>
                        @endguest
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- END Navigation -->
</header>
<!-- END Header -->
