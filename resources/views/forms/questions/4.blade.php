<div class="container">
    <h1>Question {{$number}}</h1>
    <p id="txt">How old would you be if you didn’t know how old you were? </p>
    <small>Please enter a value between {{$validation['min']}} and {{$validation['max']}}</small>
    <hr>
    <br>
    <div class="question6">
        <img id="ageImage" src="{{asset('images/ages/invalid.jpg')}}">
    </div>
    <div class="form-group age-box-div" class="disable-owl-swipe">
        <svg id="svg-age" class="disable-owl-swipe"version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"  viewBox="100 200 400 350">
            <defs>
                <filter id="goo" color-interpolation-filters="sRGB">
                    <feGaussianBlur in="SourceGraphic" stdDeviation="8" result="blur"></feGaussianBlur>
                    <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 21 -7" result="cm"></feColorMatrix>

                </filter>
            </defs>
            <g id="dragGroup">
                <path id="dragBar" fill="#2A323E" d="M447,299.5c0,1.4-1.1,2.5-2.5,2.5h-296c-1.4,0-2.5-1.1-2.5-2.5l0,0c0-1.4,1.1-2.5,2.5-2.5
		h296C445.9,297,447,298.1,447,299.5L447,299.5z"></path>
                <g id="displayGroup">
                    <g id="gooGroup" filter="url(#goo)">
                        <circle id="display" fill="#2A323E" cx="146" cy="299.5" r="16"></circle>
                        <circle id="dragger" fill="#2A323E"  stroke="#FFFFFF" stroke-width="0" cx="146" cy="299.5" r="15"></circle>
                    </g>
                    <text class="downText" x="146" y="304">0</text>
                    <text class="upText" x="145" y="266">0</text>
                </g>
            </g>
        </svg>

        <input class="disable-owl-swipe" id="age-slider" form="questionnaire_form" type="range" name="q{{$id}}"
               min="{{$validation['min']}}" max="{{$validation['max']}}" step="1" value="60" oninput="simpleSliderChange(this.value)">

        <p id="age-value">Select an age ...</p>
    </div>
</div>

<script src='https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.0/TweenMax.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.1/utils/Draggable.min.js'></script>

