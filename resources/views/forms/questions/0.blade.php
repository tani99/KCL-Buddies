<div class="gender">
    <h1>Question {{$number}}</h1>
    <p>Do you wish to be matched with students with the same gender as you?
        <small><br> We will try our best to honour all gender preferences</small>
    </p>
    <hr>
    {{--<input form="questionnaire_form" class="form-check-input" name="q{{$id}}" type="radio" id="no_preference" value="0" checked>--}}
    {{--<label class="form-check-label" for="no_preference"> No preference </label>--}}
    {{--<input form="questionnaire_form" class="form-check-input" name="q{{$id}}" type="radio" id="same_gender" value="1">--}}
    {{--<label class="form-check-label" for="same_gender"> Same Gender </label>--}}

    <div class="form-group">
        <input type="radio" name="q{{$id}}" id="no_preference" form="questionnaire_form" value="0" checked>
        <label class="custom-radio" for="no_preference"></label>
        <span class="label-text">No Preference</span>
    </div>
    <hr>
    <div class="form-group">
        <input type="radio" name="q{{$id}}" id="same_gender" form="questionnaire_form" value="1">
        <label class="custom-radio" for="same_gender"></label>
        <span class="label-text">Same Gender</span>
    </div>
</div>