<h1>Question {{$number}}
    <i class="fas fa-info-circle helpButton popover-dismiss" data-container="body" data-toggle="tooltip" data-placement="bottom"
       title="Drop a marker on the map to select your ideal travel destination"></i>
</h1>
<p>If you could travel anywhere in the world where would you go?</p>
<hr>
<div class="w-100 h-100" id="disable-owl-swipe" style="padding-bottom: 15px">
    <p class="hidden" id="coordinates">---</p>
    <input id="coordinates_lat" name="q{{$id}}-lat" type="text" value="null" form="questionnaire_form" hidden>
    <input id="coordinates_long" name="q{{$id}}-long" type="text" value="null" form="questionnaire_form" hidden>
    <input id="pac-input" class="controls" type="text" placeholder="Search for a location...">
    <div class="disable-owl-swipe" id="map"></div>
</div>