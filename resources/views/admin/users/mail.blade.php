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
                <i class="fas fa-envelope"></i> Email all
            </li>
        </ol>
    </div>

    <div class="container-fluid">
        <h2>Email users</h2>
        <hr>
        <form action="{{ route('admin.users.email.send') }}" method="post">
            @csrf
            <div class="form-group">
                <label for="subject">Subject:</label>
                <input class="form-control" id="subject" name="subject" maxlength="80">
                <small class="error">{{ $errors->first('subject') }}</small>
            </div>
            <div class="form-group">
                <label for="content-input">Email you want to send:</label>
                <textarea class="form-control" rows="15" id="content-input" name="content" maxlength="2000" required></textarea>
                <small class="error">{{ $errors->first('content') }}</small>
            </div>
            <button class="btn btn-outline-dark" style="margin-right: 5px;">Send</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-dark" style="margin-right: 5px; float: right;">Back</a>
        </form>
    </div>
@endsection
