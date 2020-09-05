@extends('layouts.app_layout')

@section('head')
    <link href="{{ asset('css/questionnaire.css') }}" rel="stylesheet" type="text/css">
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
                <i class="fas fa-users"></i> {{ $scheme->name }}
            </li>
            <li class="breadcrumb-item active">
                <i class="fas fa-book-open"></i> Join
            </li>
        </ol>
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="alert alert-danger">
                    {{ $error }}
                </div>
            @endforeach
        @endif
        <div id="terms">
            @include('inc.terms_and_conditions')
        </div>
        <div id="customNav" class="owl-nav"></div>
        <div id="customDots" class="owl-dots"></div>
        <div id="carousel" class="owl-carousel owl-theme">
            <div class="item questionnaire-item">
                <div class="card text-center min-vw-33 p-3 mw-175">
                    <div class="card-body">
                        <div id="row8" class="row justify-content-center align-items-start my-row">
                            <div class="col my-col">
                                <div class="title-buddies">
                                    <h1>Welcome to the {{$userTypeNames['singular']}} Questionnaire</h1>
                                </div>
                                <hr>
                                @if($userTypeID == 1)
                                    <h8>Thank you for your interest in the Buddy System! Please
                                        answer this questionnaire honestly and truthfully so
                                        that we can find the most appropriate buddy based on
                                        your personality. Please note you are only allowed one
                                        attempt.
                                    </h8>
                                @elseif($userTypeID == 2)
                                    <h8>Thank you for volunteering to be
                                        a {{$userTypeNames['singular']}}! Your time and
                                        effort are greatly appreciated by the students requiring
                                        your guidance and advice. Please answer this
                                        questionnaire
                                        honestly and truthfully so that we can match you to the
                                        most
                                        appropriate new student/students based on your
                                        personality.
                                        Please note you are only allowed one attempt.
                                    </h8>
                                @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @for($i = 0; $i < count($questions); ++$i)
                @php
                    $question = $questions[$i];
                    $id = $question->id;
                    $validation = $question->getValidation();
                    $number = $i + 1;
                @endphp
                <div class="item questionnaire-item">
                    <div class="card text-center">
                        <div class="container">
                            <br>
                            @include('forms.questions.' . $id)
                        </div>
                    </div>
                </div>
            @endfor
            <div class="item questionnaire-item">
                <div class="container">
                    <div class="card text-center min-vw-33 p-3 mw-175">
                        <div class="card-body">
                            <div id="row8" class="row justify-content-center align-items-start my-row">
                                <div class="col my-col">
                                    <button class="btn btn-outline-dark" data-toggle="modal"
                                            data-target="#terms-and-conditions"> Terms and Conditions
                                    </button>
                                    <hr>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="acknowledgement"
                                               id="acknowledgement" form="questionnaire_form">
                                        <label class="form-check-label" for="acknowledgement">
                                            <small>I acknowledge I have read and understood the terms and conditions.
                                            </small>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="consent" id="consent"
                                               form="questionnaire_form">
                                        <label class="form-check-label" for="consent">
                                            <small>I consent to my data being stored in accordance with the terms and
                                                conditions.
                                            </small>
                                        </label>
                                    <div class="submit-container">
                                        <br>
                                        <button id="submit_button" type="submit" form="questionnaire_form">
                                            Submit
                                        </button>
                                    </div>
                                    <form id="questionnaire_form" action="{{ route('schemes.join') }}" method="post"
                                          onsubmit="return processQuestionnaire(this);">
                                        @csrf
                                        <input type="hidden" name="join_code" value="{{ $joinCode }}">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://raw.githack.com/SortableJS/Sortable/master/Sortable.js"></script>
    <script type="text/javascript" src="{{ asset('js/questionnaire.js')}}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_KEY')}}&libraries=places&callback=initAutocomplete"
            async defer></script>
    <script type="application/javascript">
        function processQuestionnaire(form) {
            if (!form['acknowledgement'].checked) {
                alert('Please ensure you have read and understood the terms and conditions.');
                return false;
            } else if (!form['consent'].checked) {
                alert('Please ensure you consent to your data being stored in line with the conditions stated in the form.');
                return false;
            }
            @if(in_array(2, $questionIDs))
            if (!processQuestion2()) {
                alert('Invalid data for question with ID 2.');
                return false;
            }
            @endif
                return true;
        }

        function processQuestion2() {
            var simpleList = document.getElementById("simpleList");
            if (simpleList == null) return false;
            var listNodes = simpleList.getElementsByClassName("list-group-item");
            if (listNodes == null) return false;

            for (var i = 0; i < listNodes.length; i++) {
                var listNode = listNodes.item(i);
                var nodeId = listNode.id;
                var subQId = nodeId.substring(nodeId.indexOf('_') + 1);
                var inputElement = document.getElementById('q2-' + subQId);
                inputElement.value = (i + 1);
            }
            return true;
        }
    </script>
@endsection