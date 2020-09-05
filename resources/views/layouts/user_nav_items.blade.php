@section('menu_items')
    <li class="nav-item @if(Request::is('home'))active @endif">
        <a href="{{url('/home')}}"><i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item @if(Request::is('profile'))active @endif">
        <a href="{{url('/profile')}}"><i class="fas fa-user-circle"></i>
            <span>My Profile</span>
        </a>
    </li>
    <li class="nav-item @if(Request::is('schemes'))active @endif">
        <a href="{{url('/schemes')}}"><i class="fas fa-table"></i>
            <span>Schemes</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('logout') }}" onclick="event.preventDefault();
         document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i>
            <span>Sign Out</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </li>
@endsection