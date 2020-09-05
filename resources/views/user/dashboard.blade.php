@extends('layouts.app_layout')

@section('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css"/>
    <style>
        #page-content {
            font-family: roboto, sans-serif !important;
        }

        h2 {
            margin-top: 10px;
            margin-bottom: 10px;
            font-size: 1.35rem !important;
        }

        h4 {
            margin-top: 50px;
        }

        p {
            margin-top: 10px;
        }

        @media (max-width: 1102px) {
            h4 {
                font-size: 19px !important;
            }
        }

        @media (max-width: 739px) {
            h4 {
                margin-top: 40px
            }

            .img-fluid {
                max-width: 175px;
                max-height: 175px;
            }
        }

        @media (max-width: 647px) {
            h4 {
                margin-top: 50px
            }

            .img-fluid {
                max-width: 200px;
                max-height: 200px;
            }
        }

        @media (max-width: 393px) {
            h4 {
                font-size: 17px !important;
                margin-top: 40px
            }

            p {
                font-size: 14px !important;
                margin-top: 10px
            }

            .img-fluid {
                max-width: 150px;
                max-height: 150px;
            }
        }

        @media (max-width: 305px) {
            .img-fluid {
                max-width: 100px;
                max-height: 100px;
            }

            h4 {
                margin-top: 9px
            }

            p {
                margin-top: 8px;
            }
        }

        @media (max-width: 276px) {
            h4 {
                font-size: 12px !important;
                margin-top: 12px;
            }

            p {
                font-size: 11px !important;
                margin-top: 10px;
            }

            .img-fluid {
                max-width: 75px;
                max-height: 75px;
            }
        }

        @media (max-width: 260px) {
            h4 {
                margin-top: 0px;
            }

            p {
                margin-top: 7px;
            }
        }

        @media (max-width: 208px) {
            h2 {
                font-size: 17px !important;
            }

            h4 {
                font-size: 11px !important;
                margin-top: 4px;
            }

            p {
                font-size: 10px !important;
            }

            .img-fluid {
                max-width: 50px;
                max-height: 50px;
                margin-top: 12px;
                margin-left: 10px;
            }
        }

        @media (max-width: 187px) {
            .img-fluid {
                margin-top: 20px;
            }
        }

        @media (max-width: 179px) {
            .img-fluid {
                margin-top: 30px;
            }
        }

        @media (max-width: 168px) {
            .img-fluid {
                margin-top: 15px;
                margin-bottom: 8px;
                margin-left: 40px;
            }

            h4, p {
                margin-top: 0px;
                text-align: center;
            }
        }

        @media (max-width: 148px) {
            h2 {
                font-size: 15px !important;
            }

            .img-fluid {
                max-width: 45px;
                max-height: 45px;
            }
        }

        @media (max-width: 143px) {
            .img-fluid {
                margin-top: 20px;
                margin-left: 30px;
            }
        }

        @media (max-width: 140px) {
            .img-fluid {
                margin-top: 15px;
                margin-bottom: 2px;
            }
        }

        @media (max-width: 129px) {
            .img-fluid {
                margin-left: 25px;
            }
        }

        @media (max-width: 117px) {
            .img-fluid {
                margin-left: 20px;
            }
        }

        @media (max-width: 108px) {
            .img-fluid {
                margin-left: 15px;
            }
        }

        @media (max-width: 97px) {
            .img-fluid {
                margin-left: 25px;
            }
        }

        @media (max-width: 90px) {
            .img-fluid {
                margin-left: 22px;
            }
        }

        @media (max-width: 83px) {
            .img-fluid {
                margin-left: 19px;
            }
        }

        @media (max-width: 76px) {
            h2 {
                font-size: 13px !important;
            }

            .img-fluid {
                margin-left: 16px;
            }
        }

        @media (max-width: 65px) {
            .img-fluid {
                margin-top: auto;
                margin-left: auto;
            }

            h4 {
                text-align: left;
                font-size: 9px !important;
            }

            p {
                text-align: left;
                font-size: 8px !important;
            }
        }
    </style>
@endsection

@section('content')
    <div id="content-wrapper">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">
                    <i class="fas fa-home"></i> Dashboard
                </li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        @if(!empty($schemesData))
            <div id="page-content">
                <h2>Your Schemes</h2>
                <div class="owl-carousel owl-theme">
                    @foreach($schemesData as $schemeData)
                        @php
                            $scheme = $schemeData['scheme'];
                            $schemeUser = $schemeData['schemeUser'];
                        @endphp
                        <div class="item">
                            <div class="card">
                                <div class="row no-gutters">
                                    <div class="col-auto">
                                        <img src="/storage/schemes/icons/{{ $scheme->getIcon() }}" class="img-fluid" alt="Scheme image">
                                    </div>
                                    <div class="col m-2">
                                        <div class="card-block px-2">
                                            <h4 class="card-title">{{ $scheme->name }}</h4>
                                            <p class="card-text"><strong>Role:</strong> {{ $userTypesNames[$schemeUser->user_type_id]['singular'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <p class="text-center">You are not part of any schemes!</p>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script>
        $('.owl-carousel').owlCarousel({
            loop: false,
            rewind: true,
            margin: 10,
            autoplay: true,
            autoplayHoverPause: true,
            nav: true,
            responsive: {
                0: {
                    items: 1
                },
                650: {

                    items: 2
                },
                1060: {
                    items: 3
                }
            }
        });
    </script>
@endsection
