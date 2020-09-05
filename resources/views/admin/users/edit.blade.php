@extends('layouts.app_layout')

@section('head')
    <style>
        .normal-form-group {
            padding-top: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="fas fa-users"></i>
                <a href="{{route('admin.users.index')}}">Users</a>
            </li>
            <li class="breadcrumb-item">
                <i class="fas fa-user-alt"></i>
                <a href="{{route('admin.users.show', ['user_id' => $user->id])}}">{{$user->getFullName()}}</a>
            </li>
            <li class="breadcrumb-item active">
                <i class="fas fa-pencil-alt"></i> Editing
            </li>
        </ol>
    </div>

    <div class="container-fluid">
        <h2>Modify User</h2>

        <form action="{{ route('admin.users.update', ['user_id' => $user->id]) }}" method="post">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="form" for="name" style="padding-right: 5px">Name:</label>
                <input id="name" name="name" class="form-control" type="text" value="{{old('name') ? old('name') : $user->getFullName()}}" {{$user->isMicrosoftAccount() ? 'readonly' : 'required pattern="[a-zA-Z ]+"'}} maxlength="191">
            </div>
            <div class="form-group">
                <label class="form" for="email" style="padding-right: 5px">Email:</label>
                <input id="email" class="form-control" type="text" value="{{$user->email}}" readonly>
            </div>
            <small>{{$errors->first('name')}}</small>
            @if(!$user->isMicrosoftAccount())
                <div class="form-group">
                    <label class="form" for="password" style="padding-right: 5px">Password:</label>
                    <input id="password" name="password" class="form-control" type="password">
                </div>
                <small>{{$errors->first('password')}}</small>
                <div class="form-group">
                    <label class="form" for="department" style="padding-right: 5px">Department:</label>
                    <input id="department" name="department" class="form-control" type="text" value="{{old('department') ? old('department') : $user->department}}" maxlength="100">
                </div>
                <small>{{$errors->first('department')}}</small>
            @endif
            <div class="form-group">
                <label class="form" for="nickname" style="padding-right: 5px">Nickname:</label>
                <input id="nickname" name="nickname" class="form-control" type="text" value="{{old('nickname') ? old('nickname') : $user->nickname}}" maxlength="32" pattern="[a-zA-Z]*[a-zA-Z\s]*" readonly style="margin-bottom: 10px;">
                <button class="btn btn-outline-danger" onclick="return clearInput('nickname');">Clear</button>
            </div>
            <div class="form-group">
                <label class="form" for="bio" style="padding-right: 5px">Bio:</label>
                <textarea id="bio" name="bio" class="form-control" rows="3" maxlength="250" readonly style="margin-bottom: 10px;">{{old('bio') ? old('bio') : $user->bio}}</textarea>
                <button class="btn btn-outline-danger" onclick="return clearInput('bio');">Clear</button>
            </div>
            <div class="form-group">
                <label class="form" for="alt_email" style="padding-right: 5px">Alt Email:</label>
                <input id="alt_email" name="alt_email" class="form-control" type="email" value="{{old('alt_email') ? old('alt_email') : $user->alt_email}}" maxlength="191" readonly style="margin-bottom: 10px;">
                <button class="btn btn-outline-danger" onclick="return clearInput('alt_email');">Clear</button>
            </div>
            <div class="form-group" style="padding-top: 10px;">
                <button class="btn btn-outline-primary" type="submit">Save</button>
                <a class="btn btn-outline-danger" href="{{ route('admin.users.show', ['user_id' => $user->id]) }}" style="float: right;">BACK</a>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script type="application/javascript">
        function clearInput(inputElementId) {
            var inputElement = document.getElementById(inputElementId);
            if (inputElement != null) {
                inputElement.value = "";
            }
            return false;
        }
    </script>
@endsection