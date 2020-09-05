<h1>Question {{$number}}</h1>
<p>Do you wish to be matched with students of the closest age to you?</p>
<hr>
<div class="form-group">
    <input type="radio" name="q{{$id}}" id="no_age_preference" form="questionnaire_form" value="0" checked>
    <label class="custom-radio" for="no_age_preference"></label>
    <span class="label-text">No Preference</span>
</div>
<hr>
<div class="form-group">
    <input type="radio" name="q{{$id}}" id="same_age" form="questionnaire_form" value="1">
    <label class="custom-radio" for="same_age"></label>
    <span class="label-text">Same Age</span>
</div>