<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'KCL Buddies') }}</title>
    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!--favicon -->
    <link rel="icon" type="image/png" href="{{asset('images/buddy_system.png')}}">
    @yield('head')
</head>
<body>
<div class="wrapper">
    <nav id="sidebar" class="active">
        <div class="sidebar-header">
            <a class="navbar-brand" href="{{url('home')}}">
                <i class="fas fa-users"></i>
                <h5>Buddy System</h5>
            </a>
        </div>
        <ul class="list-unstyled components">
            @if((isset($accessLevel) && $accessLevel == 'sysadmin') || (isset($is_sysadmin) && $is_sysadmin))
                @include('layouts.admin_nav_items')
            @elseif(isset($accessLevel) && $accessLevel == 'user')
                @include('layouts.user_nav_items')
            @endif
            @yield('menu_items')
        </ul>
    </nav>
    <div id="content" class="active">
        <nav class="navbar navbar-expand navbar-light bg-light">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-outline-dark sidebar-toggle">
                    <i class="fas fa-bars" id="collapseButton"></i>
                </button>
            </div>
        </nav>
        <div class="container-fluid page-content">
            @yield('content')
        </div>
    </div>
</div>
<!--  Custom Scripts -->
<script type="text/javascript" src="{{ asset('js/jquery.js')}}"></script>
<script type="text/javascript" src="{{ asset('js/app.js')}}" defer></script>
<script type="text/javascript" src="{{ asset('js/profile.js') }}" defer></script>
<script type="text/javascript" src="{{ asset('js/tooltip.js') }}" defer></script>
@yield('scripts')
</body>
</html>
