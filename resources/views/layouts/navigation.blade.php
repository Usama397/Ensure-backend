<div class="d-flex align-items-center logo-box justify-content-between">
    <a href="#" class="waves-effect waves-light nav-link rounded d-none d-md-inline-block mx-10 push-btn"
       data-toggle="push-menu" role="button">
        <span class="icon-Align-left"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span>
    </a>
    <!-- Logo -->
    <a href="{{ route('dashboard') }}" class="logo">
        <!-- logo-->
        {{--<div class="logo-lg">
            <span class="light-logo"><img src="{{ asset('custom/img/econceptions.png') }}" alt="logo"></span>
            <span class="dark-logo"><img src="{{ asset('custom/img/econceptions.png') }}" alt="logo"></span>
        </div>--}}
    </a>
</div>

<!-- Header Navbar -->
<nav class="navbar navbar-static-top pl-10">
    <!-- Sidebar toggle button-->
    <div class="app-menu">
        <ul class="header-megamenu nav">
            <li class="btn-group nav-item d-md-none">
                <a href="#" class="waves-effect waves-light nav-link rounded push-btn" data-toggle="push-menu"
                   role="button">
                    <span class="icon-Align-left"><span class="path1"></span><span class="path2"></span><span
                            class="path3"></span></span>
                </a>
            </li>
            <li class="btn-group nav-item d-xl-inline-block">
                @if(isset($header))
                    {{ $header }}
                @endif
            </li>
        </ul>
    </div>

    <div class="navbar-custom-menu r-side">
        <ul class="nav navbar-nav">

            <li class="btn-group nav-item d-lg-inline-flex">
                <a href="#" data-provide="fullscreen" class="waves-effect waves-light nav-link rounded full-screen"
                   title="Full Screen">
                    <i class="icon-Expand-arrows"><span class="path1"></span><span class="path2"></span></i>
                </a>
            </li>

            <!-- User Account-->
            <li class="dropdown user user-menu">
                <a href="#" class="waves-effect waves-light dropdown-toggle" data-toggle="dropdown">
                    <i class="icon-User"><span class="path1"></span><span class="path2"></span></i>
                </a>
                <ul class="dropdown-menu animated flipInX">
                    <li class="user-body">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault();
                                        this.closest('form').submit();">
                                <i class="ti-lock text-muted mr-2"></i>
                                {{ __('Logout') }}
                            </a>
                        </form>
                        <a href="{{ route('changePassword') }}" class="dropdown-item">
                            <i class="ti-lock text-muted mr-2"></i>
                            {{ __('Change Password') }}
                        </a>
                    </li>
                </ul>
            </li>

        </ul>
    </div>
</nav>
