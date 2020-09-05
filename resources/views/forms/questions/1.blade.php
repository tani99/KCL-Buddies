<h1>Question {{$number}}</h1>
<p id="txt">How much do you like these cuisines?
    <br><small>Rank from 1 - 5</small>
</p>
<hr>
<div class="cusines">
    <div class="row">
        @php
            $cuisines = [
                'Indian' => 'cuisineSlider1',
                'Chinese' => 'cuisineSlider1',
                'American' => 'cuisineSlider1',
                'Italian' => 'cuisineSlider1',
                'Japanese' => 'cuisineSlider1',
            ];
            $cuisine_names = array_keys($cuisines);
        @endphp

        @for($i = 0; $i < count($cuisine_names); ++$i)
            @php
                $cuisine_name = $cuisine_names[$i];
            @endphp
            <input form="questionnaire_form" id="input_{{$i}}" name="q{{$id}}-{{$i}}" type="text" value="0" hidden>
            <div class="col-10 col-5 cuisines">
                <div class="cuisine">
                    <span class="{{strtolower($cuisine_name)}} cusine-emoji"></span>
                    <p class="cusine-label">{{ $cuisine_name }}</p>
                    <div class="stars" data-rating="3" data-slider-value="3">
                        <span id="{{$i}}" class="star star-{{$i}} star1 rated">&nbsp;</span>
                        <span id="{{$i}}" class="star star-{{$i}} star2 rated">&nbsp;</span>
                        <span id="{{$i}}" class="star star-{{$i}} star3 rated">&nbsp;</span>
                        <span id="{{$i}}" class="star star-{{$i}} star4">&nbsp;</span>
                        <span id="{{$i}}" class="star star-{{$i}} star5">&nbsp;</span>
                    </div>
                </div>
            </div>
        @endfor
    </div>
</div>


{{--@for($i = 0; $i < count($cuisine_names); ++$i)--}}
{{--@php--}}
{{--$cuisine_name = $cuisine_names[$i];--}}
{{--@endphp--}}
{{--<div class="{{$cuisines[$cuisine_name] }}">--}}
{{--<h8>{{ $cuisine_name }}</h8>--}}
{{--<input form="questionnaire_form" name="q{{$id}}-{{$i}}" type="range" max="2" min="-2" class="focused">--}}
{{--<img src="{{ url('/images/cuisines/'. strtolower($cuisine_name) .'.jpg')}}" width="100px" height="100px">--}}
{{--</div>--}}
{{--@endfor--}}



