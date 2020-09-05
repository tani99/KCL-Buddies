@extends('layouts.app_layout')

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <i class="fas fa-users"></i> Users
            </li>
        </ol>
        @if(isset($success))
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{ $success }}
            </div>
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="alert alert-danger alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {{ $error }}
                </div>
            @endforeach
        @endif
    </div>

    <div class="container-fluid">
        <h2>Your Account</h2>
        <form action="{{ route('admin.user.update_credentials') }}" method="post">
            @csrf
            <div class="form-group">
                <label for="name">Name:</label>
                <input class="form-control" id="name" name="name" type="text" value="{{$adminUser->getFullName()}}">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input class="form-control" id="password" name="password" type="password">
            </div>
            <div class="form-group">
                <button class="btn btn-outline-primary">Update</button>
            </div>
        </form>
    </div>
    <hr>
    <div class="container-fluid">
        <h2>Users</h2>
        <div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-outline-info" style="float: right; margin-bottom: 10px">New User</a>
            @if($canEmail)
                <a href="{{ route('admin.users.email.index') }}" class="btn btn-outline-primary" style="float: right; margin-bottom: 10px; margin-right: 5px;">Email</a>
            @endif
            <br>
            <br>
        </div>
        <div class="no-more-tables">
            <table class="table table-bordered">
                <thead class="thead-dark">
                <tr>
                    <th width="25%">Name</th>
                    <th width="50%">Email</th>
                    <th width="25%">Actions</th>
                </tr>
                </thead>
                <tbody>
                @if(!empty($users))
                    @foreach($users as $user)
                        <tr>
                            <td data-title="Name">{{$user->getFullName() . (isset($user->nickname) ? ' (' . $user->nickname . ')' : '')}}</td>
                            <td data-title="Email"><a href="mailto:{{$user->email}}">{{$user->email}}</a></td>
                            <td data-title="Actions">
                                <div class="input-group">
                                    <a href="{{ route('admin.users.show', ['user_id' => $user->id]) }}" class="btn btn-outline-primary" style="margin-right: 5px;">View</a>
                                    @if($user->banned)
                                        <a href="{{ route('admin.users.toggle_ban', ['user_id' => $user->id]) }}" class="btn btn-outline-warning" style="margin-right: 5px;">Unban</a>
                                    @else
                                        <a href="{{ route('admin.users.toggle_ban', ['user_id' => $user->id]) }}" class="btn btn-outline-danger" style="margin-right: 5px;" onclick="return confirm('Are you sure you want to ban \'{{ $user->getFullName() }}\'?');">Ban</a>
                                    @endif
                                    <a href="{{ route('admin.users.edit', ['user_id' => $user->id]) }}" class="btn btn-outline-secondary" style="margin-right: 5px;">Edit</a>
                                    @if(!$user->isMicrosoftAccount())
                                        <form action="{{ route('admin.users.destroy', ['user_id' => $user->id]) }}" method="post" onsubmit="return confirm('Are you sure you want to delete \'{{ $user->getFullName() }}\'?');">
                                            @method('DELETE')
                                            @csrf
                                            <button class="btn btn-outline-danger" type="submit">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center" colspan="3">There are no users.</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection