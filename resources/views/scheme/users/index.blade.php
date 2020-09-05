@extends('layouts.app_layout')

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="fas fa-table"></i>
                <a href="{{route('schemes.index')}}">Schemes</a>
            </li>
            <li class="breadcrumb-item">
                <i class="fas fa-users"></i>
                <a href="{{route('schemes.show', ['scheme_id' => $scheme->id])}}">{{ $scheme->name }}</a>
            </li>
            <li class="breadcrumb-item active">
                <i class="fas fa-eye"></i> Users
            </li>
        </ol>
        @if(isset($success))
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{ $success }}
            </div>
        @endif
    </div>

    <div class="container-fluid">
        <div>
            <h2>Users</h2>
            <div style="padding-bottom: 5px;">
                <a href="{{ route('schemes.users.kick_all', ['scheme_id' => $scheme->id]) }}" class="btn btn-outline-dark" style="float: right;" onclick="return confirm('Are you sure you want to kick all users?');">Kick all</a>
                <a href="{{ route('schemes.users.approve_all', ['scheme_id' => $scheme->id]) }}" class="btn btn-outline-primary" style="float: right; margin-right: 5px;" onclick="return confirm('Are you sure you want to approve all users?');">Approve all</a>
                @if($canEmail)
                    <a href="{{ route('schemes.email.users.index', ['scheme_id' => $scheme->id]) }}" class="btn btn-outline-secondary" style="float: right; margin-right: 5px;">Email</a>
                @endif
                <br>
                <br>
            </div>
            <div class="no-more-tables">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th width="19%">Name</th>
                        <th width="27%">Email</th>
                        <th width="13%">Phone Number</th>
                        <th width="8%">Group</th>
                        <th width="8%">Join Date</th>
                        <th width="25%">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($users))
                        @foreach($users as $userID => $userData)
                            @php($user = $userData['user'])
                            <tr>
                                <td data-title="Name">{{$user->getFullName() . (isset($user->nickname) ? ' (' . $user->nickname . ')' : '')}}</td>
                                <td data-title="Email">
                                    @if(isset($user->alt_email))
                                        <ul>
                                            <li><a href="mailto:{{$user->email}}">{{$user->email}}</a></li>
                                            <li><a href="mailto:{{$user->alt_email}}">{{$user->alt_email}}</a></li>
                                        </ul>
                                    @else
                                        <a href="mailto:{{$user->email}}">{{$user->email}}</a>
                                    @endif
                                </td>
                                <td data-title="Phone Number">{{isset($user->phone_number) ? $user->phone_number : 'Unspecified'}}</td>
                                <td data-title="Type">{{$userData['userTypeName']}}</td>
                                <td data-title="Join Date">{{$userData['joinDate']}}</td>
                                <td data-title="Actions">
                                    @if($canApproveUsers === true && $userData['approved'] == false)
                                        <a href="{{ route('schemes.users.approve', ['scheme_user_id' => $userData['schemeUserID']]) }}" class="btn btn-outline-primary" onclick="return confirm('Are you sure you want to approve this user?');">Approve</a>
                                    @endif
                                    @if($canKickUsers === true)
                                        <a href="{{ route('schemes.users.kick', ['scheme_user_id' => $userData['schemeUserID']]) }}" class="btn btn-outline-dark" onclick="return confirm('Are you sure you want to kick that user?');">Kick</a>
                                    @endif
                                    @if($canBanUsers === true)
                                        <a href="{{ route('schemes.users.ban_existing', ['scheme_user_id' => $userData['schemeUserID']]) }}" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to ban that user?');">Ban</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center">No users have signed up to this scheme.</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <h2>Banned Users</h2>
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
                    @if(!empty($bannedUsers))
                        @foreach($bannedUsers as $userID => $bannedUser)
                            <tr>
                                <td data-title="Name">{{$bannedUser->getFullName() . (isset($bannedUser->nickname) ? ' (' . $bannedUser->nickname . ')' : '')}}</td>
                                <td data-title="Email"><a href="mailto:{{$bannedUser->email}}">{{$bannedUser->email}}</a></td>
                                <td data-title="Actions">
                                    @if($canBanUsers === true)
                                        <a href="{{ route('schemes.users.unban', ['scheme_id' => $scheme->id, 'user_id' => $userID]) }}" class="btn btn-warning" onclick="return confirm('Are you sure you want to unban that user?');">Unban</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" class="text-center">No users have been banned from this scheme.</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

        <a href="{{ route('schemes.show', ['scheme_id' => $scheme->id]) }}" class="btn btn-outline-dark" style="float: right;">Return to scheme</a>
    </div>
@endsection
