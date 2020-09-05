<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon -->
    <link rel="icon" type="image/png" href="{{asset('images/buddy_system.png')}}">
    <title>{{ config('app.name', 'KCL Buddies') }}</title>
    <!-- Styles -->
    <link rel="stylesheet" href="{{asset('css/welcome.css')}}">
</head>

<body>
<div id="particles-js"></div>
<div class="flex-center position-ref full-height">
    @if(Route::has('welcome'))
        <div class="top-right links">
            @auth
                <a href="{{ url('/home') }}">Home</a>
            @else
            @endauth
        </div>
    @endif
    <div class="content auth">
        <div class="container" >
            @if(Route::has('welcome'))
                @auth
                    <div class="title m-b-md">
                        Buddy System
                    </div>
                @else
                    <div class="card auth">
                        <h4 class="text-center">Login</h4>
                        <div class="card-body">
                            <div class="tab-content w-100" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                    @include('auth.login_form')
                                </div>
                            </div>
                        </div>
                    </div>
                @endauth
            @endif
        </div>
    </div>
</div>

<script src="{{asset('js/welcome.js')}}"></script>
<script src="{{asset('js/particles.min.js')}}"></script>
<script>
    particlesJS.load('particles-js', '{{asset('js/pc2.json')}}');
</script>
</body>
</html>