@extends('layouts.app_layout')

@section('head')
    <style>
        @media (min-width: 0) {
            .card-deck .card {
                flex: 0 0 calc(100% - 30px);
            }

            .card {
                margin: 0 auto;
                float: none;
                margin-bottom: 10px;
            }
        }

        @media (min-width: 200px) {
            h5, p, .list-group-item {
                font-size: 0.8rem;
            }

            .card-img-top {
                width: 175px;
                height: 175px;
            }
        }

        @media (min-width: 350px) {
            .card-deck .card {
                flex: 0 0 calc(50% - 30px);
                max-width: calc(50% - 30px);
            }

            h5, p, .list-group-item {
                font-size: 0.84rem;
            }
        }

        @media (min-width: 768px) {
            h5, p, .list-group-item {
                font-size: 1rem;
            }

            .card-img-top {
                width: 200px;
                height: 200px;
            }
        }

        @media (min-width: 992px) {
            .card-deck .card {
                flex: 0 0 calc(33.33333% - 30px);
                max-width: calc(33.33333% - 30px);
            }
        }


        @media (min-width: 1200px) {
            .card-img-top {
                width: 256px;
                height: 256px;
            }
        }

        .card-img-top {
            border-radius: 50%;
            max-width: 256px;
            max-height: 256px;
            margin-top: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
            border: 2px solid grey;
        }

        h5 {
            font-weight: bold;
            text-align: center;
        }

        h3 {
            margin-top: 10px;
            margin-bottom: 10px;
            font-size: 1.35rem !important;
        }
    </style>
@endsection

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
        <h2>Your Pairings</h2>
    </div>

    @foreach($pairingUsers as $userTypeID => $userTypePairings)
        <div class="container-fluid">
            <h3>{{ $userTypeNames[$userTypeID]['plural'] }}</h3>
            <div class="card-deck">
                @foreach ($userTypePairings as $user)
                    @php
                        $userPreferences = $usersPreferences[$user->id];
                    @endphp
                    <div class="card my-3">
                        <img src="/storage/avatars/{{ $user->getAvatar() }}" class="card-img-top" alt="{{ $user->getFullName() }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $user->getFullName() . (isset($user->nickname) ? ' (' . $user->nickname . ')' : '') }}</h5>
                            @if(isset($user->bio))
                                <p class="card-text">{{ $user->bio }}</p>
                            @endif
                            <ul class="list-group list-group-flush text-center">
                                <li class="list-group-item">
                                    <strong>Email:</strong> <a href="mailto:{{$user->email}}">{{ $user->email }}</a>
                                </li>
                                @if(isset($user->alt_email))
                                    <li class="list-group-item">
                                        <strong>Alternative Email:</strong> <a href="mailto:{{$user->alt_email}}">{{ $user->alt_email }}</a>
                                    </li>
                                @endif
                                @if(isset($user->phone_number))
                                    <li class="list-group-item">
                                        <strong>Phone:</strong> {{ $user->phone_number }}
                                    </li>
                                @endif
                                @if(isset($user->department))
                                    <li class="list-group-item">
                                        <strong>Department:</strong> {{ $user->department }}
                                    </li>
                                @endif
                                @if(!$userPreferences->gender_private && $user->gender != 4)
                                    <li class="list-group-item">
                                        <strong>Gender:</strong> {{ $user->getGender() }}
                                    </li>
                                @endif
                                @if(isset($user->birthdate) && !$userPreferences->birthdate_private)
                                    <li class="list-group-item">
                                        <strong>Age:</strong> {{ $user->getAge() }}
                                    </li>
                                @endif
                                @if(isset($user->country))
                                    <li class="list-group-item">
                                        <strong>Country:</strong> {{ $user->country }}
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
@endsection
