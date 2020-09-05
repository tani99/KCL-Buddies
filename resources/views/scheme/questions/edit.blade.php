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
                <i class="fas fa-question"></i>
                <a href="{{route('schemes.questions.index', ['scheme_id' => $scheme->id])}}">Questions</a>
            </li>
            <li class="breadcrumb-item active">
                <i class="fas fa-pencil-alt"></i> Editing
            </li>
        </ol>
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="alert alert-danger">
                    {{ $error }}
                </div>
            @endforeach
        @endif
    </div>

    <div class="container-fluid">
        <h2>Questions <i class="fas fa-question-circle popover-dismiss" data-container="body" data-toggle="tooltip" data-placement="right"
                         title="Move questions to the left hand side in order to add them to the questionnaire. Use the arrows to alter the positioning of questions within the questionnaire, or to remove questions entirely."></i>
        </h2>
        <div style="margin: 5px 0 10px;">
            <form action="{{ route('schemes.questions.update', ['scheme_id' => $scheme->id]) }}" method="post" onsubmit="return processQuestions();">
                @csrf
                <div class="input-group" style="margin-bottom: 5px;">
                    <select class="form-control" id="active_questions_id" size="15" name="active_questions[]" multiple style="margin-right: 5px">
                        @foreach($currentQuestions as $questionID => $questionData)
                            <option value="{{$questionID}}" class="text-center">{{$questionData['title']}}</option>
                        @endforeach
                    </select>
                    <div class="form-group" style="display: inline-grid; position: relative; vertical-align: middle !important;">
                        @if($canChange === true)
                            <button class="btn btn-outline-info" type="button" onclick="removeQuestion(); return true;">
                                &gt
                            </button>
                            <br>
                            <button class="btn btn-outline-info" type="button" onclick="addQuestion(); return true;">&lt
                            </button>
                            <br>
                        @endif
                        <button class="btn btn-outline-dark" type="button" onclick="moveQuestionUp(); return true;">
                            &uarr;
                        </button>
                        <br>
                        <button class="btn btn-outline-dark" type="button" onclick="moveQuestionDown(); return true;">
                            &darr;
                        </button>
                    </div>
                    <select class="form-control" id="all_questions_id" size="15" multiple style="margin-left: 5px">
                        @foreach($allQuestions as $questionID => $questionData)
                            <option value="{{$questionID}}" class="text-center">{{$questionData['title']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <button class="btn btn-outline-success" type="submit">SAVE</button>
                    <a href="{{ route('schemes.questions.index', ['scheme_id' => $scheme->id]) }}" class="btn btn-outline-danger" style="float: right;">CANCEL</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="application/javascript">
        function addQuestion() {
            var allQuestionsElement = document.getElementById("all_questions_id");
            var activeQuestionsElement = document.getElementById("active_questions_id");
            var optionsToRemove = [];
            for (var i = 0; i < allQuestionsElement.selectedOptions.length; i++) {
                var questionOption = allQuestionsElement.selectedOptions[i];
                var questionId = questionOption.value;
                var questionTitle = questionOption.innerText;
                optionsToRemove.push(questionOption);

                var newQuestion = document.createElement('option');
                newQuestion.className = "text-center";
                newQuestion.value = questionId;
                newQuestion.innerHTML = questionTitle;
                newQuestion.selected = true;
                activeQuestionsElement.appendChild(newQuestion);
            }
            for (var j = 0; j < optionsToRemove.length; j++) {
                allQuestionsElement.removeChild(optionsToRemove[j]);
            }
        }

        function removeQuestion() {
            var activeQuestionsElement = document.getElementById("active_questions_id");
            var currIndex = activeQuestionsElement.selectedIndex;
            if (currIndex !== -1) {
                var questionOption = activeQuestionsElement[currIndex];
                var questionId = questionOption.value;
                var questionTitle = questionOption.innerText;
                activeQuestionsElement.removeChild(questionOption);

                var allQuestionsElement = document.getElementById("all_questions_id");
                var newQuestion = document.createElement('option');
                newQuestion.className = "text-center";
                newQuestion.value = questionId;
                newQuestion.innerHTML = questionTitle;
                allQuestionsElement.appendChild(newQuestion);

                if (currIndex < activeQuestionsElement.options.length) {
                    activeQuestionsElement[currIndex].selected = true;
                } else if (activeQuestionsElement.options.length > 0) {
                    activeQuestionsElement[activeQuestionsElement.options.length - 1].selected = true;
                }
            }
        }

        function moveQuestionUp() {
            var activeQuestionsElement = document.getElementById("active_questions_id");
            var currIndex = activeQuestionsElement.selectedIndex;
            if (currIndex > 0) {
                var questionOption = activeQuestionsElement[currIndex];
                activeQuestionsElement.removeChild(questionOption);
                activeQuestionsElement.insertBefore(questionOption, activeQuestionsElement[currIndex - 1]);
            }
        }

        function moveQuestionDown() {
            var activeQuestionsElement = document.getElementById("active_questions_id");
            var currIndex = activeQuestionsElement.selectedIndex;
            if (currIndex < activeQuestionsElement.options.length - 1) {
                var questionOption = activeQuestionsElement[currIndex];
                activeQuestionsElement.removeChild(questionOption);
                activeQuestionsElement.insertBefore(questionOption, activeQuestionsElement[currIndex + 1]);
            }
        }

        function processQuestions() {
            var activeQuestionsElement = document.getElementById("active_questions_id");
            if (activeQuestionsElement.options.length < 5) {
                alert('Please ensure there are at least 5 questions in the questionnaire.');
                return false;
            }
            for (var i = 0; i < activeQuestionsElement.options.length; i++) {
                var questionOption = activeQuestionsElement[i];
                questionOption.selected = true;
            }
            return true;
        }
    </script>
@endsection
