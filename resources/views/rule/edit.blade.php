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
            <li class="breadcrumb-item">
                <i class="fas fa-eye"></i>
                <a href="{{route('rules.index', ['scheme_id' => $scheme->id])}}">Rules</a>
            </li>
            <li class="breadcrumb-item active">
                <i class="fas fa-pencil-alt"></i> Editing
            </li>
        </ol>
    </div>

    <div class="container-fluid">
        <h2>Rules</h2>
        <form id="rules_form" action="{{ route('rules.update', ['scheme_id' => $scheme->id]) }}" method="post">
            @csrf
            @method('PUT')
            <table class="table table-borderless">
                <tbody>
                @foreach($rulesData as $ruleID => $ruleData)
                    @php
                        $ruleData['nameLc'] = str_replace(' ', '_', strtolower($ruleData['name']));
                        $ruleData['oldValue'] = old($ruleData['nameLc']);
                    @endphp
                    @include('forms.rules.rule', $ruleData)
                @endforeach
                </tbody>
            </table>
            <input value="SAVE" class="btn btn-outline-success" type="submit">
            <a href="{{ route('rules.index', ['scheme_id' => $scheme->id]) }}" class="btn btn-outline-danger" style="float: right;">CANCEL</a>
        </form>
    </div>
@endsection