@php
    $potentialInterests =
    [
        'Cricket',
        'Basketball',
        'Volleyball',
        'Table-Tennis',
        'Tennis',
        'Swimming',
        'Hockey',
        'Rugby',
        'E-Sports',
        'Football'
    ];
@endphp

<h1>Question {{$number}}</h1>
<p>Select all the activities you are interested in.</p>
<hr>
<input form="questionnaire_form" type="hidden" name="q{{$id}}-count" value="{{count($potentialInterests)}}">

<div class="options-sports">
    {{--<div class="row-activities">--}}
    <div class="row">
        @for($i = 0; $i < count($potentialInterests); ++$i)
            <div class="col-5 col-sm-2">
                <div class="sport" value="{{$i}}">
                    <input form="questionnaire_form" type="checkbox" id="cb{{$i + 1}}" name="q{{$id}}-{{$i}}"/>
                    <label for="cb{{$i + 1}}">
                        <img style="padding: 30px;" src="{{ asset('images/sports/'. str_replace('-', '', strtolower($potentialInterests[$i])) . '.png') }}">
                        <h3 class="sports-label">{{$potentialInterests[$i]}}</h3>
                    </label>
                </div>
            </div>
        @endfor

    </div>
</div>