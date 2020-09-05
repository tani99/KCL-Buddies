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
                <i class="fas fa-pencil-alt"></i> New User
            </li>
        </ol>
    </div>

    <div class="container-fluid w-75">
        <form action="{{ route('admin.users.store') }}" method="post">
                    @csrf

                    <div class="form-group row">
                        <label for="name" class="text-md-right">{{ __('Name') }}</label>
                        <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus>
                        @if($errors->has('name'))
                            <span class="invalid-feedback" role="alert"><strong>{{ $errors->first('name') }}</strong></span>
                        @endif
                    </div>

                    <div class="form-group row">
                        <label for="email-register" class="text-md-right">{{ __('E-Mail Address') }}</label>
                        <input id="email-register" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>
                        @if($errors->has('email'))
                            <span class="invalid-feedback" role="alert"><strong>{{ $errors->first('email') }}</strong></span>
                        @endif
                    </div>

                    <div class="form-group row">
                        <label for="password-register" class="text-md-right">{{ __('Password') }}</label>
                        <input id="password-register" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                        @if($errors->has('password'))
                            <span class="invalid-feedback" role="alert"><strong>{{ $errors->first('password') }}</strong></span>
                        @endif
                    </div>

                    <div class="form-group row">
                        <label for="password-confirm" class="text-md-right">{{ __('Confirm Password') }}</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                    </div>


                        <button type="submit" class="btn btn-outline-dark">
                            {{ __('Create') }}
                        </button>

                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-danger" style="float:right">CANCEL</a>


        </form>
            </div>


@endsection