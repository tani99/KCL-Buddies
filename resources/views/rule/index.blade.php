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
                <i class="fas fa-eye"></i> Rules
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
        <h2>Rules</h2>
        @if(!empty($rulesData))
            <table class="table">
                @foreach($rulesData as $ruleData)
                    @php
                        $rule = $ruleData[0];
                        $ruleValue = $ruleData[1];
                    @endphp
                    <tr>
                        <th width="10%">{{ $rule->name }}</th>
                        <td width="70%">{{ $rule->description }}</td>
                        <td width="20%">{{ $ruleValue }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
        <a class="btn btn-outline-success" href="{{ route('rules.edit', ['scheme_id' => $scheme->id]) }}">EDIT</a>
        <a href="{{ route('schemes.show', ['scheme_id' => $scheme->id]) }}" class="btn btn-outline-dark" style="float: right;">BACK</a>
    </div>
@endsection