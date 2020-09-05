@php
    $favouriteActivities = [
        'Going to a restaurant/cafe',
        'Going to a concert',
        'Going to a club',
        'Playing video games',
        'Watching a movie',
        'Studying'
    ];
@endphp

<h1>Question {{$number}}</h1>
<p>What are your favorite activities to do with friends?</p>
<small>Drag the following activities in order of preference, with your most preferred activity to do with friends at the top, and your least preferred at the bottom.</small>
<hr>
<div class="question5" class="disable-owl-swipe">
    <div class="rank-indicators">Most preferred</div>
    @for($i = 0; $i < count($favouriteActivities); ++$i)
        <input form="questionnaire_form" id="q{{$id}}-{{$i}}" name="q{{$id}}-{{$i}}" type="hidden" value="{{$i + 1}}">
    @endfor
    <div id="simpleList" class="disable-owl-swipe list-group">
        @for($i = 0; $i < count($favouriteActivities); ++$i)
            <div class="list-group-item" id="act_{{$i}}">{{$favouriteActivities[$i]}}</div>
        @endfor
    </div>
    <div class="rank-indicators">Least preferred</div>
</div>