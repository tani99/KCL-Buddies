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
                <i class="fas fa-eye"></i> Pairings
            </li>
        </ol>
        @if(isset($success))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ $success }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
    </div>

    <div class="container-fluid">
        <div id="actions">
            <form action="{{ route('schemes.pairs.destroy_all', ['scheme_id' => $scheme->id]) }}" method="post">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-outline-danger" style="float: right">Unpair all</button>
            </form>
            @if($canEmail)
                <a href="{{ route('schemes.email.users.index', ['scheme_id' => $scheme->id]) }}" class="btn btn-outline-primary" style="float: right; margin-right: 5px;">Email</a>
            @endif
        </div>

        <div>
            <h2>Pairings ({{count($pairings)}})</h2>
            <div class="no-more-tables">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th width="45%">{{$userTypeNames[2]['plural']}}</th>
                        <th width="45%">{{$userTypeNames[1]['plural']}}</th>
                        <th width="10%">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($pairings))
                        @foreach($pairings as $pairingID => $pairing)
                            @php
                                $buddies = $pairing[0];
                                $newbies = $pairing[1];
                            @endphp
                            @if(!empty($buddies) && !empty($newbies))
                                <tr>
                                    <td data-title="Buddies" style="padding: 0; border-color: black;">
                                        <div class="no-more-tables">
                                            <table class="table table-bordered">
                                                <tbody>
                                                @foreach($buddies as $buddy)
                                                    <tr>
                                                        <td width="40%">{{ $buddy->getFullName() . (isset($buddy->nickname) ? ' (' . $buddy->nickname . ')' : '') }}</td>
                                                        <td width="60%"><a href="mailto:{{$buddy->email}}">{{ $buddy->email }}</a></td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                    <td data-title="Newbies" style="padding: 0; border-color: black;">
                                        <div class="no-more-tables">
                                            <table class="table table-bordered">
                                                @foreach($newbies as $newbie)
                                                    <tr>
                                                        <td width="40%">{{ $newbie->getFullName() . (isset($newbie->nickname) ? ' (' . $newbie->nickname . ')' : '') }}</td>
                                                        <td width="60%"><a href="mailto:{{$newbie->email}}">{{ $newbie->email }}</a></td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </td>
                                    <td data-title="Actions" class="text-center" style="padding: 0; border-color: black; vertical-align: middle;">
                                        <form action="{{ route('schemes.pairs.destroy', ['scheme_id' => $scheme->id, 'pairing_id' => $pairingID]) }}" method="post" onsubmit="return confirm('Are you sure you want to unpair these users?');">
                                            @method('DELETE')
                                            @csrf
                                            <button type="submit" class="btn btn-danger">Unpair</button>
                                        </form>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" class="text-center">There are no pairings.</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

        @if(!empty($unpaired))
            <div>
                <h2>Unpaired users ({{count($unpaired)}})</h2>
                <div class="no-more-tables">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                        <tr>
                            <th width="25%">Name</th>
                            <th width="45%">Email</th>
                            <th width="30%">Type</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($unpaired as $unpairedUser)
                            <tr>
                                <td data-title="Name">{{ $unpairedUser['user']->getFullName() . (isset($unpairedUser['user']->nickname) ? ' (' . $unpairedUser['user']->nickname . ')' : '') }}</td>
                                <td data-title="Email"><a href="mailto:{{$unpairedUser['user']->email}}">{{ $unpairedUser['user']->email }}</a></td>
                                <td data-title="Type">{{ $unpairedUser['userTypeName'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @endif
        <a href="{{ route('schemes.show', ['scheme_id' => $scheme->id]) }}" class="btn btn-outline-dark" style="float: right;">Return to scheme</a>
    </div>
@endsection
