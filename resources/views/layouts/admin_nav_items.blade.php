@section('menu_items')
    <li class="nav-item @if(Request::is('admin/home'))active @endif">
        <a href="{{url('admin/home')}}">
            <i class="fas fa-home"></i>
            <span>Admin Dashboard</span>
        </a>
    </li>
    <li class="nav-item @if(Request::is('schemes'))active @endif">
        <a href="{{url('/schemes')}}"><i class="fas fa-table"></i>
            <span>Scheme Management</span>
        </a>
    </li>
    <li class="nav-item @if(Request::is('admin/users'))active @endif">
        <a href="{{url('admin/users')}}"><i class="fas fa-users"></i>
            <span>User Management</span>
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