@extends('layouts.app_layout')

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="fas fa-home"></i>
                <a href="{{route('user.home')}}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">
                <i class="fa fa-user-circle fa-fw"></i> My Profile
            </li>
        </ol>
    </div>

    <div class="container-fluid">
        <div id="page-content-wrapper">
            <div class="row">
                <div class="col-xs-12 col-sm-12">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul style="margin-bottom: 0 !important;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @elseif(isset($success))
                        <div class="alert alert-success alert-dismissible">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            {{ $success }}
                        </div>
                    @endif

                    <form id="delete_profile_picture_form" action="{{ route('user.profile.delete_profile_picture') }}"
                          method="post">
                        @method('DELETE')
                        @csrf
                    </form>

                    <form action="{{ route('user.profile.save') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="profile-header-container align-content-center text-center">
                            <div class="profile-header-img col-md-3 mx-auto" style="width: 256px; height: 256px;">
                                <img id="profile_picture" class="rounded-circle img-fluid"
                                     src="/storage/avatars/{{ $user->getAvatar() }}" alt="User avatar">
                            </div>
                            <div style="padding-top: 10px">
                                <label class="btn btn-default btn-dark btn-file">
                                    Change
                                    <input id="profile_picture_input" type="file" style="display: none;" name="avatar"
                                           onchange="updateProfilePicture(this);" accept=".png,.jpeg,.jpg">
                                </label>
                                <label class="btn btn-default btn-danger" onclick="return deleteProfilePicture();">
                                    &times;
                                </label>
                            </div>
                            <small id="profile_picture_warning">Image resolution must be 256x256 and file size
                                must be <= 2MB
                            </small>
                        </div>
                        <div class="panel panel-default">
                            <i class="fas fa-address-card" style="font-size: 25px"></i>
                            <a class="h4">User Info</a>
                            <hr>
                            <small>
                                Provide any additional information you would like others to know about you. Your profile
                                will only be available to administrators and to any buddies/newbies you are successfully
                                matched to. You can opt to make certain information private, meaning it will not be
                                visible to anyone at all.
                                Note that providing this information is completely optional and will only be used in
                                order to facilitate contact between you and your matches. Please note that entering
                                inappropriate or offensive information is prohibited; administrators have the power to
                                edit your profile or ban you from schemes should this issue arise.
                                <br><br>
                            </small>
                            <div class="container-fluid">
                                <div class="form-group">
                                    <label for="name" class="form-inline" style="padding-right: 5px;">Full
                                        Name:</label>
                                    <input type="text" class="form-control col-sm-3" id="name"
                                           value="{{ old('name') ? old('name') : $user->getFullName() }}"
                                           readonly>
                                </div>
                                <div class="form-group">
                                    <label for="nickname_input" class="form-inline" style="padding-right: 5px;">Nickname: <i class="fas fa-question-circle popover-dismiss" data-container="body" data-toggle="tooltip" data-placement="right"
                                                                                                                             title="If you prefer to be addressed by another name, please input it here. This will allow your matched buddies/newbies to know how they should address you."></i></label>
                                    <input type="text" class="form-control col-sm-2" id="nickname_input"
                                           name="nickname" placeholder="Nickname" maxlength="32"
                                           pattern="[a-zA-Z]*[a-zA-Z\s]*"
                                           value="{{ old('nickname') ? old('nickname') : $user->nickname }}"/>

                                </div>
                                @if(!$user->isMicrosoftAccount())
                                    <div class="form-group">
                                        <label for="department_input">Department:</label>
                                        <input type="text" class="form-control col-sm-2" id="department_input"
                                               name="department" placeholder="Department" maxlength="100"
                                               pattern="[a-zA-Z ]*"
                                               value="{{ old('department') ? old('department') : (isset($user->department) ? $user->department : '') }}"/>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label for="bio_input">Bio: <i class="fas fa-question-circle popover-dismiss" data-container="body" data-toggle="tooltip" data-placement="right"
                                                                   title="Please provide any additional information you want your assigned buddies/newbies to know about you. Any information entered will be visible to your matched buddies/newbies, as well as to the administrators. "></i></label>
                                    <textarea class="form-control" id="bio_input" name="bio"
                                              placeholder="Add something about yourself..." rows="3"
                                              maxlength="250">{{ old('bio') ? old('bio') : $user->bio }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="birthdate_input" class="form-inline"
                                           style="padding-right: 5px;">Birthday: <i class="fas fa-question-circle popover-dismiss" data-container="body" data-toggle="tooltip" data-placement="right"
                                                                                    title="We recommend providing your birthday, as it allows us to determine your age. This is useful in cases where you request to be matched to students in your same age group, or in cases where other students are looking for a buddy in the same age range. You can choose to have it publicly or privately displayed, so privacy is not an issue. "></i></label>

                                    <input type="text" class='datepicker form-control col-sm-2' name="birthdate" data-date-format="YYYY/MM/DD"  value="{{ old('birthdate') ? old('birthdate') : $user->birthdate }}">

                                    <div class="form-check" style="padding-top: 5px;">
                                        <input class="form-check-input" type="checkbox" name="birth_date_private" id="birth_date_private" value="1"{{ $preferences->birthdate_private ? ' checked' : '' }}>
                                        <label class="form-check-label" for="birth_date_private">
                                            Keep this information private <i class="fas fa-question-circle popover-dismiss" data-container="body" data-toggle="tooltip" data-placement="right"
                                                                             title="Unchecking this box will make your birthday public (visible to your assigned buddies/newbies and to administrators). Leave this box checked if you want this information to remain private (visible only to yourself)."></i>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="gender_input" class="form-inline"
                                           style="padding-right: 5px;">Gender: <i class="fas fa-question-circle popover-dismiss" data-container="body" data-toggle="tooltip" data-placement="right"
                                                                                  title="We recommend providing your gender. This is useful in cases where you request to be matched to students of your same gender, or in cases where other students are looking for a buddy of the same gender. You can choose to have it publicly or privately displayed, so privacy is not an issue."></i></label>
                                    <select class="form-control col-sm-2" id="gender_input" name="gender">
                                        <option value="1">Male</option>
                                        <option value="2">Female</option>
                                        <option value="3">Other</option>
                                        <option value="4">Prefer not to say</option>
                                    </select>
                                    <div class="form-check" style="padding-top: 5px;">
                                        <input class="form-check-input" type="checkbox" name="gender_private" id="gender_private" value="1"{{ $preferences->gender_private ? ' checked' : '' }}>
                                        <label class="form-check-label" for="gender_private">
                                            Keep this information private <i class="fas fa-question-circle popover-dismiss" data-container="body" data-toggle="tooltip" data-placement="right"
                                                                             title="Unchecking this box will make your gender  public (visible to your assigned buddies/newbies and to administrators). Leave this box checked if you want this information to remain private (visible only to yourself)."></i>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="country_input" class="form-inline" style="padding-right: 5px;">Country: <i class="fas fa-question-circle popover-dismiss" data-container="body" data-toggle="tooltip" data-placement="right"
                                                                                                                           title="Enter your country if you wish for your assigned buddies/newbies to see see where you are from."></i></label>
                                    <select class="form-control col-sm-3" id="country_input" name="country">
                                        <option value="" selected>-</option>
                                        <option value="Afghanistan">Afghanistan</option>
                                        <option value="Albania">Albania</option>
                                        <option value="Algeria">Algeria</option>
                                        <option value="American Samoa">American Samoa</option>
                                        <option value="Andorra">Andorra</option>
                                        <option value="Angola">Angola</option>
                                        <option value="Anguilla">Anguilla</option>
                                        <option value="Antigua &amp; Barbuda">Antigua &amp; Barbuda</option>
                                        <option value="Argentina">Argentina</option>
                                        <option value="Armenia">Armenia</option>
                                        <option value="Aruba">Aruba</option>
                                        <option value="Australia">Australia</option>
                                        <option value="Austria">Austria</option>
                                        <option value="Azerbaijan">Azerbaijan</option>
                                        <option value="Bahamas">Bahamas</option>
                                        <option value="Bahrain">Bahrain</option>
                                        <option value="Bangladesh">Bangladesh</option>
                                        <option value="Barbados">Barbados</option>
                                        <option value="Belarus">Belarus</option>
                                        <option value="Belgium">Belgium</option>
                                        <option value="Belize">Belize</option>
                                        <option value="Benin">Benin</option>
                                        <option value="Bermuda">Bermuda</option>
                                        <option value="Bhutan">Bhutan</option>
                                        <option value="Bolivia">Bolivia</option>
                                        <option value="Bonaire">Bonaire</option>
                                        <option value="Bosnia &amp; Herzegovina">Bosnia &amp; Herzegovina</option>
                                        <option value="Botswana">Botswana</option>
                                        <option value="Brazil">Brazil</option>
                                        <option value="British Indian Ocean Ter">British Indian Ocean Ter</option>
                                        <option value="Brunei">Brunei</option>
                                        <option value="Bulgaria">Bulgaria</option>
                                        <option value="Burkina Faso">Burkina Faso</option>
                                        <option value="Burundi">Burundi</option>
                                        <option value="Cambodia">Cambodia</option>
                                        <option value="Cameroon">Cameroon</option>
                                        <option value="Canada">Canada</option>
                                        <option value="Canary Islands">Canary Islands</option>
                                        <option value="Cape Verde">Cape Verde</option>
                                        <option value="Cayman Islands">Cayman Islands</option>
                                        <option value="Central African Republic">Central African Republic</option>
                                        <option value="Chad">Chad</option>
                                        <option value="Channel Islands">Channel Islands</option>
                                        <option value="Chile">Chile</option>
                                        <option value="China">China</option>
                                        <option value="Christmas Island">Christmas Island</option>
                                        <option value="Cocos Island">Cocos Island</option>
                                        <option value="Colombia">Colombia</option>
                                        <option value="Comoros">Comoros</option>
                                        <option value="Congo">Congo</option>
                                        <option value="Cook Islands">Cook Islands</option>
                                        <option value="Costa Rica">Costa Rica</option>
                                        <option value="Cote DIvoire">Cote D'Ivoire</option>
                                        <option value="Croatia">Croatia</option>
                                        <option value="Cuba">Cuba</option>
                                        <option value="Curaco">Curacao</option>
                                        <option value="Cyprus">Cyprus</option>
                                        <option value="Czech Republic">Czech Republic</option>
                                        <option value="Denmark">Denmark</option>
                                        <option value="Djibouti">Djibouti</option>
                                        <option value="Dominica">Dominica</option>
                                        <option value="Dominican Republic">Dominican Republic</option>
                                        <option value="East Timor">East Timor</option>
                                        <option value="Ecuador">Ecuador</option>
                                        <option value="Egypt">Egypt</option>
                                        <option value="El Salvador">El Salvador</option>
                                        <option value="Equatorial Guinea">Equatorial Guinea</option>
                                        <option value="Eritrea">Eritrea</option>
                                        <option value="Estonia">Estonia</option>
                                        <option value="Ethiopia">Ethiopia</option>
                                        <option value="Falkland Islands">Falkland Islands</option>
                                        <option value="Faroe Islands">Faroe Islands</option>
                                        <option value="Fiji">Fiji</option>
                                        <option value="Finland">Finland</option>
                                        <option value="France">France</option>
                                        <option value="French Guiana">French Guiana</option>
                                        <option value="French Polynesia">French Polynesia</option>
                                        <option value="French Southern Ter">French Southern Ter</option>
                                        <option value="Gabon">Gabon</option>
                                        <option value="Gambia">Gambia</option>
                                        <option value="Georgia">Georgia</option>
                                        <option value="Germany">Germany</option>
                                        <option value="Ghana">Ghana</option>
                                        <option value="Gibraltar">Gibraltar</option>
                                        <option value="Great Britain">Great Britain</option>
                                        <option value="Greece">Greece</option>
                                        <option value="Greenland">Greenland</option>
                                        <option value="Grenada">Grenada</option>
                                        <option value="Guadeloupe">Guadeloupe</option>
                                        <option value="Guam">Guam</option>
                                        <option value="Guatemala">Guatemala</option>
                                        <option value="Guinea">Guinea</option>
                                        <option value="Guyana">Guyana</option>
                                        <option value="Haiti">Haiti</option>
                                        <option value="Hawaii">Hawaii</option>
                                        <option value="Honduras">Honduras</option>
                                        <option value="Hong Kong">Hong Kong</option>
                                        <option value="Hungary">Hungary</option>
                                        <option value="Iceland">Iceland</option>
                                        <option value="India">India</option>
                                        <option value="Indonesia">Indonesia</option>
                                        <option value="Iran">Iran</option>
                                        <option value="Iraq">Iraq</option>
                                        <option value="Ireland">Ireland</option>
                                        <option value="Isle of Man">Isle of Man</option>
                                        <option value="Israel">Israel</option>
                                        <option value="Italy">Italy</option>
                                        <option value="Jamaica">Jamaica</option>
                                        <option value="Japan">Japan</option>
                                        <option value="Jordan">Jordan</option>
                                        <option value="Kazakhstan">Kazakhstan</option>
                                        <option value="Kenya">Kenya</option>
                                        <option value="Kiribati">Kiribati</option>
                                        <option value="Korea North">Korea North</option>
                                        <option value="Korea South">Korea South</option>
                                        <option value="Kuwait">Kuwait</option>
                                        <option value="Kyrgyzstan">Kyrgyzstan</option>
                                        <option value="Laos">Laos</option>
                                        <option value="Latvia">Latvia</option>
                                        <option value="Lebanon">Lebanon</option>
                                        <option value="Lesotho">Lesotho</option>
                                        <option value="Liberia">Liberia</option>
                                        <option value="Libya">Libya</option>
                                        <option value="Liechtenstein">Liechtenstein</option>
                                        <option value="Lithuania">Lithuania</option>
                                        <option value="Luxembourg">Luxembourg</option>
                                        <option value="Macau">Macau</option>
                                        <option value="Macedonia">Macedonia</option>
                                        <option value="Madagascar">Madagascar</option>
                                        <option value="Malaysia">Malaysia</option>
                                        <option value="Malawi">Malawi</option>
                                        <option value="Maldives">Maldives</option>
                                        <option value="Mali">Mali</option>
                                        <option value="Malta">Malta</option>
                                        <option value="Marshall Islands">Marshall Islands</option>
                                        <option value="Martinique">Martinique</option>
                                        <option value="Mauritania">Mauritania</option>
                                        <option value="Mauritius">Mauritius</option>
                                        <option value="Mayotte">Mayotte</option>
                                        <option value="Mexico">Mexico</option>
                                        <option value="Midway Islands">Midway Islands</option>
                                        <option value="Moldova">Moldova</option>
                                        <option value="Monaco">Monaco</option>
                                        <option value="Mongolia">Mongolia</option>
                                        <option value="Montserrat">Montserrat</option>
                                        <option value="Morocco">Morocco</option>
                                        <option value="Mozambique">Mozambique</option>
                                        <option value="Myanmar">Myanmar</option>
                                        <option value="Namibia">Namibia</option>
                                        <option value="Nauru">Nauru</option>
                                        <option value="Nepal">Nepal</option>
                                        <option value="Netherlands Antilles">Netherlands Antilles</option>
                                        <option value="Netherlands">Netherlands</option>
                                        <option value="Nevis">Nevis</option>
                                        <option value="New Caledonia">New Caledonia</option>
                                        <option value="New Zealand">New Zealand</option>
                                        <option value="Nicaragua">Nicaragua</option>
                                        <option value="Niger">Niger</option>
                                        <option value="Nigeria">Nigeria</option>
                                        <option value="Niue">Niue</option>
                                        <option value="Norfolk Island">Norfolk Island</option>
                                        <option value="Norway">Norway</option>
                                        <option value="Oman">Oman</option>
                                        <option value="Pakistan">Pakistan</option>
                                        <option value="Palau Island">Palau Island</option>
                                        <option value="Palestine">Palestine</option>
                                        <option value="Panama">Panama</option>
                                        <option value="Papua New Guinea">Papua New Guinea</option>
                                        <option value="Paraguay">Paraguay</option>
                                        <option value="Peru">Peru</option>
                                        <option value="Phillipines">Philippines</option>
                                        <option value="Pitcairn Island">Pitcairn Island</option>
                                        <option value="Poland">Poland</option>
                                        <option value="Portugal">Portugal</option>
                                        <option value="Puerto Rico">Puerto Rico</option>
                                        <option value="Qatar">Qatar</option>
                                        <option value="Republic of Montenegro">Republic of Montenegro</option>
                                        <option value="Republic of Serbia">Republic of Serbia</option>
                                        <option value="Reunion">Reunion</option>
                                        <option value="Romania">Romania</option>
                                        <option value="Russia">Russia</option>
                                        <option value="Rwanda">Rwanda</option>
                                        <option value="St Barthelemy">St Barthelemy</option>
                                        <option value="St Eustatius">St Eustatius</option>
                                        <option value="St Helena">St Helena</option>
                                        <option value="St Kitts-Nevis">St Kitts-Nevis</option>
                                        <option value="St Lucia">St Lucia</option>
                                        <option value="St Maarten">St Maarten</option>
                                        <option value="St Pierre &amp; Miquelon">St Pierre &amp; Miquelon</option>
                                        <option value="St Vincent &amp; Grenadines">St Vincent &amp; Grenadines</option>
                                        <option value="Saipan">Saipan</option>
                                        <option value="Samoa">Samoa</option>
                                        <option value="Samoa American">Samoa American</option>
                                        <option value="San Marino">San Marino</option>
                                        <option value="Sao Tome &amp; Principe">Sao Tome &amp; Principe</option>
                                        <option value="Saudi Arabia">Saudi Arabia</option>
                                        <option value="Senegal">Senegal</option>
                                        <option value="Serbia">Serbia</option>
                                        <option value="Seychelles">Seychelles</option>
                                        <option value="Sierra Leone">Sierra Leone</option>
                                        <option value="Singapore">Singapore</option>
                                        <option value="Slovakia">Slovakia</option>
                                        <option value="Slovenia">Slovenia</option>
                                        <option value="Solomon Islands">Solomon Islands</option>
                                        <option value="Somalia">Somalia</option>
                                        <option value="South Africa">South Africa</option>
                                        <option value="Spain">Spain</option>
                                        <option value="Sri Lanka">Sri Lanka</option>
                                        <option value="Sudan">Sudan</option>
                                        <option value="Suriname">Suriname</option>
                                        <option value="Swaziland">Swaziland</option>
                                        <option value="Sweden">Sweden</option>
                                        <option value="Switzerland">Switzerland</option>
                                        <option value="Syria">Syria</option>
                                        <option value="Tahiti">Tahiti</option>
                                        <option value="Taiwan">Taiwan</option>
                                        <option value="Tajikistan">Tajikistan</option>
                                        <option value="Tanzania">Tanzania</option>
                                        <option value="Thailand">Thailand</option>
                                        <option value="Togo">Togo</option>
                                        <option value="Tokelau">Tokelau</option>
                                        <option value="Tonga">Tonga</option>
                                        <option value="Trinidad &amp; Tobago">Trinidad &amp; Tobago</option>
                                        <option value="Tunisia">Tunisia</option>
                                        <option value="Turkey">Turkey</option>
                                        <option value="Turkmenistan">Turkmenistan</option>
                                        <option value="Turks &amp; Caicos Is">Turks &amp; Caicos Is</option>
                                        <option value="Tuvalu">Tuvalu</option>
                                        <option value="Uganda">Uganda</option>
                                        <option value="Ukraine">Ukraine</option>
                                        <option value="United Arab Erimates">United Arab Emirates</option>
                                        <option value="United Kingdom">United Kingdom</option>
                                        <option value="United States of America">United States of America</option>
                                        <option value="Uraguay">Uruguay</option>
                                        <option value="Uzbekistan">Uzbekistan</option>
                                        <option value="Vanuatu">Vanuatu</option>
                                        <option value="Vatican City State">Vatican City State</option>
                                        <option value="Venezuela">Venezuela</option>
                                        <option value="Vietnam">Vietnam</option>
                                        <option value="Virgin Islands (Brit)">Virgin Islands (Brit)</option>
                                        <option value="Virgin Islands (USA)">Virgin Islands (USA)</option>
                                        <option value="Wake Island">Wake Island</option>
                                        <option value="Wallis &amp; Futuna Is">Wallis &amp; Futuna Is</option>
                                        <option value="Yemen">Yemen</option>
                                        <option value="Zaire">Zaire</option>
                                        <option value="Zambia">Zambia</option>
                                        <option value="Zimbabwe">Zimbabwe</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="panel panel-default">
                            <i class="fas fa-address-book" style="font-size: 25px"></i>
                            <a class="h4">Contact Info</a>
                            <hr>
                            <div class="container-fluid">
                                <div class="form-group">
                                    <label for="email" class="form-inline"
                                           style="padding-right: 5px;">Email:</label>
                                    <input type="text" class="form-control col-sm-3" id="email"
                                           value="{{ $user->email }}" readonly/>
                                </div>
                                <div class="form-group">
                                    <label for="alt_email_input" class="form-inline"
                                           style="padding-right: 5px;">Alternative
                                        Email: <i class="fas fa-question-circle popover-dismiss" data-container="body" data-toggle="tooltip" data-placement="right"
                                                  title="You may provide an additional email where your assigned buddies/newbies may contact you if they are unable to reach you at your KCL email."></i></label>
                                    <input type="email" class="form-control col-sm-3" id="alt_email_input"
                                           name="alt_email"
                                           value="{{ old('alt_email') ? old('alt_email') : $user->alt_email }}"/>
                                </div>
                                <div class="form-group">
                                    <label for="phone_number_input" class="form-inline"
                                           style="padding-right: 5px;">Phone
                                        Number: <i class="fas fa-question-circle popover-dismiss" data-container="body" data-toggle="tooltip" data-placement="right"
                                                   title="If you wish to allow your assigned buddies/newbies to contact you by phone/text please provide your phone number here."></i></label>
                                    <input type="text" class="form-control col-sm-2" id="phone_number_input"
                                           name="phone_number"
                                           value="{{ old('phone_number') ? old('phone_number') : $user->phone_number }}"
                                           maxlength="12" pattern="([+][0-9])?[0-9]*"/>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <i class="fas fa-book-open" style="font-size: 25px"></i>
                            <a class="h4">Preferences</a>
                            <hr>
                            <div class="container-fluid">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="email_notifs_input" name="email_notifs" value="1"{{ $preferences->email_notifs ? ' checked' : '' }}>
                                    <label class="form-check-label" for="email_notifs_input">Email Notifications <i class="fas fa-question-circle popover-dismiss" data-container="body" data-toggle="tooltip" data-placement="right"
                                                                                                                    title="Uncheck this box if you do not wish to receive email messages related to the buddy system. Note that this will prevent you from receiving emails from any schemes you are signed up for. It is possible that by opting out of email notifications you may miss out on important updates from your scheme administrators."></i></label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Update Changes</button>
                        </div>
                        <hr>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="application/javascript">
        document.getElementById("gender_input").selectedIndex = {{ $user->gender - 1 }};

        @if(isset($user->country))
        selectOptionByValue(document.getElementById("country_input"), "{{ $user->country }}");

        @endif

        function selectOptionByValue(selectElement, value) {
            var options = selectElement.options;
            var optionsLength = options.length;
            for (var i = 0; i < optionsLength; i++) {
                if (options[i].value === value) {
                    selectElement.selectedIndex = i;
                    return true;
                }
            }
            return false;
        }
    </script>

    <script type="application/javascript">
        function updateProfilePicture(profilePictureInput) {
            if (profilePictureInput.files) {
                var profilePictureFile = profilePictureInput.files[0];
                if (profilePictureFile.size < 2097152) {
                    var reader = new FileReader();
                    var profilePicture = document.getElementById("profile_picture");
                    reader.onload = function (e) {
                        var img = new Image();
                        img.onload = function () {
                            if (img.width === 256 && img.height === 256) {
                                profilePicture.src = e.target.result;
                            } else {
                                alert('Please upload an image with resolution 256x256');
                            }
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(profilePictureFile);
                } else {
                    profilePictureInput.value = null;
                    alert('The image file size must be <= 2MB');
                }
            }
        }

        function deleteProfilePicture() {
            @if(isset($user->avatar))
            if (confirm('Are you sure you want to remove your profile picture?')) {
                document.getElementById("delete_profile_picture_form").submit();
            }
                    @else
            var profilePictureInput = document.getElementById("profile_picture_input");
            if (profilePictureInput.value !== '') {
                if (confirm('Are you sure you want to remove that file?')) {
                    profilePictureInput.value = null;

                    var profilePicture = document.getElementById("profile_picture");
                    profilePicture.src = '/storage/avatars/{{ $user->getDefaultAvatar() }}';
                }
            } else {
                alert('You have not set a profile picture!');
            }
            @endif
        }
    </script>


    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


