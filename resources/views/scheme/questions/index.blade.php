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
                <i class="fas fa-users"></i>
                <a href="{{route('schemes.show', ['scheme_id' => $scheme->id])}}">{{ $scheme->name }}</a>
            </li>
            <li class="breadcrumb-item active">
                <i class="fas fa-eye"></i> Questions
            </li>
        </ol>
        @if(isset($success))
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{ $success }}
            </div>
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="alert alert-danger">
                    {{ $error }}
                </div>
            @endforeach
        @endif
    </div>
    <div class="container-fluid">
        <h2>Questions</h2>
        <div>
            <a href="{{ route('schemes.questions.edit', ['scheme_id' => $scheme->id]) }}" class="btn btn-outline-info" style="float: right;">Edit</a>
            <br>
            <br>
        </div>
        <form id="questionnaire_form">
        </form>
        <form id="questions_weighting_form" action="{{ route('schemes.questions.update_weightings', ['scheme_id' => $scheme->id]) }}" method="post">
            @csrf
        </form>
        <div style="margin: 5px 0px 10px;">
            @if(count($questions) > 0)
                <div id="accordion" style="margin-bottom: 5px">
                    @for($i = 0; $i < count($questions); ++$i)
                        @php
                            $questionData = $questions[$i];
                            $id = $questionData['id'];
                            $number = $i + 1;
                            $validation = $questionData['validation'];
                        @endphp
                        <div class="card">
                            <div class="card-header" id="heading{{$number}}">
                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse{{$number}}" aria-expanded="false" aria-controls="collapse{{$number}}">
                                    {{$questionData['title']}}
                                </button>
                                <div style="float: right;">
                                    <label for="q{{$id}}-weight">Weighting: <i class="fas fa-question-circle popover-dismiss" data-container="body" data-toggle="tooltip" data-placement="right"
                                                                               title="Set the weighting of this question relative to the others. For example, a question with a weighting of 2.00 will have twice as much impact on the pairing process as a question with the default weighting of 1.00."></i>
                                    </label>
                                    <input id="q{{$id}}-weight" name="q{{$id}}-weight" type="number" min="0" max="9999.99" step="0.1" value="{{$questionData['weight']}}" form="questions_weighting_form" required>
                                </div>
                            </div>

                            <div id="collapse{{$number}}" class="collapse" aria-labelledby="heading{{$number}}" data-parent="#accordion">
                                <div class="text-center min-vw-33 p-3 mw-175">
                                    <div id="row8" class="row justify-content-center align-items-start my-row">
                                        <div class="col my-col">
                                            <div class="question-space">
                                                @include('forms.questions.' . $id)
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
                <button class="btn btn-outline-success" type="submit" form="questions_weighting_form">Save</button>
            @else
                <p>There are no questions.</p>
            @endif
        </div>

        <a href="{{ route('schemes.show', ['scheme_id' => $scheme->id]) }}" class="btn btn-outline-dark" style="float: right;">Return to scheme</a>
    </div>
@endsection

@section('scripts')
    <script src="https://raw.githack.com/SortableJS/Sortable/master/Sortable.js"></script>
    <script type="text/javascript" src="{{ asset('js/questionnaire.js')}}"></script>
@endsection
