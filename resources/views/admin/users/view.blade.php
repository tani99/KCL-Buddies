@extends('layouts.app_layout')

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="fas fa-users"></i>
                <a href="{{route('admin.users.index')}}">Users</a>
            </li>
            <li class="breadcrumb-item active">
                <i class="fas fa-eye"></i> {{$user->getFullName()}}
            </li>
        </ol>
    </div>

    <div class="container-fluid">
        @if(isset($success))
            <div class="alert alert-success">
                {{ $success }}
            </div>
        @endif
            <div id="user">
            <h2>User</h2>
            <div class="profile-header-container align-content-center text-center">
                <div class="profile-header-img col-md-3 mx-auto" style="width: 256px; height: 256px;">
                    <img id="profile_picture" class="rounded-circle img-fluid" src="/storage/avatars/{{ $user->getAvatar() }}" alt="User avatar">
                </div>
            </div>
                <table class="table">
                    <tbody>
                    <tr>
                        <th width="20%">Name</th>
                        <td>{{ $user->getFullName() . (isset($user->nickname) ? ' (' . $user->nickname . ')' : '') }}</td>
                    </tr>
                    <tr>
                        <th width="20%">Email</th>
                        <td><a href="mailto:{{$user->email}}">{{ $user->email }}</a></td>
                    </tr>
                    @if(isset($user->department))
                        <tr>
                            <th width="20%">Department</th>
                            <td>{{ $user->department }}</td>
                        </tr>
                    @endif
                    @if(isset($user->phone_number))
                        <tr>
                            <th width="20%">Phone Number</th>
                            <td>{{ $user->phone_number }}</td>
                        </tr>
                    @endif
                    @if(isset($user->alt_email))
                        <tr>
                            <th width="20%">Alt Email</th>
                            <td>{{ $user->alt_email }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th width="20%">Join Date</th>
                        <td>{{ $joinDate }}</td>
                    </tr>
                    </tbody>
                </table>

            <div class="input-group">
                <a href="{{ route('admin.users.edit', ['user_id' => $user->id]) }}" class="btn btn-outline-primary" style="margin-right: 5px">Edit</a>
                @if($user->banned)
                    <a href="{{ route('admin.users.toggle_ban', ['user_id' => $user->id]) }}" class="btn btn-outline-info" style="margin-right: 5px">Unban</a>
                @else
                    <a href="{{ route('admin.users.toggle_ban', ['user_id' => $user->id]) }}" class="btn btn-outline-danger" style="margin-right: 5px" onclick="return confirm('Are you sure you want to ban this user?');">Ban</a>
                @endif
                @if(!$user->isMicrosoftAccount())
                    <form action="{{ route('admin.users.destroy', ['user_id' => $user->id]) }}" method="post" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        @method('DELETE')
                        @csrf
                        <button class="btn btn-danger" type="submit">Delete</button>
                    </form>
                @endif
            </div>
        </div>

        <div id="schemes" style="padding-top: 10px">
            <h2>Schemes</h2>
            <div class="no-more-tables">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th width="20%">Name</th>
                        <th width="50%">Description</th>
                        <th width="10%">Group</th>
                        <th width="20%">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($schemesData))
                        @foreach($schemesData as $schemeData)
                            @php
                                $scheme = $schemeData['scheme'];
                                $schemeUser = $schemeData['schemeUser'];
                            @endphp
                            <tr>
                                <td data-title="Name"><a href="{{ route('schemes.show', ['scheme_id' => $scheme->id]) }}">{{ $scheme->name }}</a></td>
                                <td data-title="Description">{{ $scheme->description }}</td>
                                <td data-title="Group">{{ $userTypesNames[$schemeUser->user_type_id]['singular'] }}</td>
                                <td data-title="Actions">
                                    <a href="{{ route('schemes.users.kick', ['scheme_user_id' => $schemeUser->id]) }}" class="btn btn-outline-info" onclick="return confirm('Are you sure you want to kick that user from that scheme?');">Kick</a>
                                    <a href="{{ route('schemes.users.ban_existing', ['scheme_user_id' => $schemeUser->id]) }}" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to ban that user from that scheme?');">Ban</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center" colspan="4">This user has not joined any schemes.</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        <a class="btn btn-outline-danger" href="{{ route('admin.users.index') }}" style="float: right;">BACK</a>
    </div>
@endsection