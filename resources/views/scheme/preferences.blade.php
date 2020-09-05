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
                <i class="fas fa-pencil-alt"></i> Preferences
            </li>
        </ol>
    </div>

    <div class="container-fluid">
        <h2>Preferences</h2>
        <form action="{{ route('schemes.preferences.update', ['scheme_id' => $scheme->id]) }}" method="post">
            @csrf
            @if($canChangeMaxNewbies)
                <div style="padding-bottom: 10px;">
                    <div class="form-group" style="padding-bottom: 5px;">
                        <label for="max_newbies" class="form-inline" style="padding-right: 5px;">Select the maximum number of newbies you want to be a buddy for:</label>
                        <select id="max_newbies" name="max_newbies" class="form-control">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <small class="error">{{$errors->first('max_newbies')}}</small>
                </div>
            @endif
            <div class="form-check" style="padding-bottom: 5px;">
                <input id="subscribed" name="subscribed" class="form-check-input" type="checkbox" value="1" {{($preferences['subscribed'] ?? true) ? 'checked' : ''}}>
                <label for="subscribed" class="form-check-label" style="padding-right: 5px;">Subscribe to email notifications from this scheme</label>
            </div>
            <small class="error">{{$errors->first('subscribed')}}</small>
            <div class="form-group">
                <button class="btn btn-primary" type="submit">Save</button>
                <a href="{{ route('schemes.show', ['scheme_id' => $scheme->id]) }}" class="btn btn-danger" style="float: right;">Cancel</a>
            </div>
        </form>
    </div>
@endsection
