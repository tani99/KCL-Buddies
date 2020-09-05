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
                <i class="fas fa-envelope"></i> Emailing users
            </li>
        </ol>
    </div>

    <div class="container-fluid">
        <h2>Email participants</h2>
        <hr>
        <form action="{{ route('schemes.email.users.send', ['scheme_id' => $scheme->id]) }}" method="post">
            @csrf
            <div class="form-group">
                <label for="options">Who do you want to email?</label>
                <br>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-outline-secondary active">
                        <input type="radio" name="user_type" id="everyone" autocomplete="off" value="-1" checked> Everyone
                    </label>
                    @foreach($userTypes as $userTypeId => $userType)
                        @php($userTypeNameLc = strtolower($userType->getNames()['plural']))
                        <label class="btn btn-outline-secondary">
                            <input type="radio" name="user_type" id="{{$userTypeNameLc}}" autocomplete="off" value="{{$userTypeId}}"> {{$userType->getNames()['plural']}}
                        </label>
                    @endforeach
                </div>
                <br>
                <small class="error">{{ $errors->first('user_type') }}</small>
            </div>
            <div class="form-group">
                <label for="subject">Subject:</label>
                <input class="form-control" id="subject" name="subject" maxlength="80">
                <small class="error">{{ $errors->first('subject') }}</small>
            </div>
            <div class="form-group">
                <label for="content-input">Email you want to send:</label>
                <textarea class="form-control" rows="15" id="content-input" name="content" maxlength="1000" required></textarea>
                <small class="error">{{ $errors->first('content') }}</small>
            </div>
            <button class="btn btn-outline-dark" style="margin-right: 5px;">Send</button>
            <a href="{{ route('schemes.users.index', ['scheme_id' => $scheme->id]) }}" class="btn btn-outline-dark" style="margin-right: 5px; float: right;">Back</a>
        </form>
    </div>
@endsection
