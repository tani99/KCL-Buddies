@extends('layouts.app_layout')

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="fas fa-table"></i>
                <a href="{{route('schemes.index')}}">Schemes</a>
            </li>
            @if(isset($schemeID))
                <li class="breadcrumb-item">
                    <i class="fas fa-users"></i>
                    <a href="{{route('schemes.show', ['scheme_id' => $schemeID])}}">{{ $name }}</a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="fas fa-pencil-alt"></i> {{ 'Editing' }}
                </li>
            @else
                <li class="breadcrumb-item active">
                    <i class="fas fa-pencil-alt"></i> {{ 'New Scheme' }}
                </li>
            @endif
        </ol>
        @if(isset($success))
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{ $success }}
            </div>
        @endif
    </div>

    <div class="container-fluid" style="padding-bottom: 10px;">
        <h4>{{ isset($schemeID) ? 'Edit Scheme' : 'New Scheme' }}</h4>
        @if(isset($schemeID))
            <form id="delete_icon_form" action="{{ route('schemes.delete_icon', ['scheme_id' => $schemeID]) }}" method="post">
                @method('DELETE')
                @csrf
            </form>
        @endif
        <form id="scheme_form" action="{{ isset($schemeID) ? route('schemes.update', ['scheme_id' => $schemeID]) : route('schemes.store') }}" method="post" enctype="multipart/form-data">
            @if(isset($schemeID))
                @method('PUT')
            @endif
            @csrf
            <div class="text-center">
                <div class="mx-auto" style="width: 200px; height: 200px;">
                    <img id="icon" class="img-fluid" src="/storage/schemes/icons/{{ isset($icon) ? $icon : \App\Scheme::getDefaultIcon() }}" alt="Scheme icon">
                </div>
                <div style="padding-top: 10px;">
                    <label class="btn btn-default btn-dark btn-file">
                        Change
                        <input id="icon_input" type="file" style="display: none;" name="icon" onchange="updateIcon(this);" accept=".png,.jpeg,.jpg">
                    </label>
                    <label class="btn btn-default btn-danger" onclick="return deleteIcon();">
                        &times;
                    </label>
                </div>
                @if(strlen($errors->first('icon')) > 0)
                    <small class="error">{{$errors->first('icon')}}</small>
                @else
                    <small id="icon_warning">Image resolution must be 200x200 and file size must be <= 2MB</small>
                @endif
            </div>
            <table class="table table-borderless">
                <tbody>
                <tr>
                    <th width="15%"><label for="name_input">Name</label></th>
                    <td>
                        <input id="name_input" name="name" class="form-control" type="text" value="{{ old('name') ? old('name') : ($name ?? '') }}" required>
                        <small class="error">{{ $errors->first('name') }}</small>
                    </td>
                </tr>
                <tr>
                    <th width="15%"><label for="description_input">Description</label></th>
                    <td>
                        <textarea id="description_input" name="description" class="form-control" rows="3" required>{{ old('description') ? old('description') : ($description ?? '') }}</textarea>
                        <small class="error">{{ $errors->first('description') }}</small>
                    </td>
                </tr>
                <tr>
                    <th width="15%"><label for="type_id_input">Type <i class="fas fa-question-circle popover-dismiss" data-container="body" data-toggle="tooltip" data-placement="right"
                                                                       title="Please select the scheme type. “Deadline” schemes will automatically pair registered users on the day of the deadline. “Waves” schemes will match users at several points based on intervals selected by the admin under “scheme preferences”.  “Manual” schemes require the administrator to manually press the “Pair” button in order to create the matches. "></i></label></th>
                    <td>
                        <select id="type_id_input" name="type_id" class="form-control">
                            @foreach($schemeTypes as $schemeTypeID => $schemeType)
                                <option value="{{$schemeTypeID}}" {{ ((old('type_id') && old('type_id') == $schemeTypeID) || (isset($typeID) && $typeID == $schemeTypeID)) ? 'selected' : '' }}>{{$schemeType->name}}</option>
                            @endforeach
                        </select>
                        <small class="error">{{ $errors->first('type_id') }}</small>
                    </td>
                </tr>
                @if($accessLevel === 'sysadmin')
                    <tr>
                        <th width="15%"><label for="departments_input">Departments</label></th>
                        <td>
                            <textarea id="departments_input" name="departments" hidden></textarea>
                            <div id="departments_input_container">
                                <div class="input-group" style="padding-bottom: 5px;">
                                    <input id="departments_selector_input" class="form-control" type="text" placeholder="Name" style="margin-right: 5px;" onkeydown="onInputListKeyDown(event);" onkeyup="onInputListKeyUp(event, 'departments_selector_add');">
                                    <span id="departments_selector_add" onclick="newDepartment()" class="btn btn-outline-primary">Add</span>
                                </div>
                                <ul id="departments_list" class="list-group" style="margin-bottom: 0">
                                    @if((old('departments') && strlen(old('departments')) > 0) || (isset($departments) && strlen($departments) > 0))
                                        @foreach(explode(PHP_EOL, old('departments') ? old('departments') : $departments) as $department)
                                            @if(strlen(trim($department)) > 0)
                                                <li class="list-group-item list-group-item-warning">{{ $department }}
                                                    <span class="close" onclick="removeListElement(this, 'departments_input', '{{$department}}')">&times;</span>
                                                </li>
                                            @endif
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                            <small class="error">{{ $errors->first('departments') }}</small>
                        </td>
                    </tr>
                @endif
                <tr>
                    <th width="15%"><label for="date_start_input">Start Date</label></th>
                    <td>
                        <input type="text" class="form-control datepicker"  class='form-control col-sm-2' name="date_start" data-date-format="YYYY/MM/DD"  value="{{ old('date_start') ? old('date_start') : ($dateStartInput ?? '') }}">
                        <small class="error">{{ $errors->first('date_start') }}</small>
                    </td>
                </tr>
                <tr>
                    <th width="15%"><label for="date_end_input">End Date</label></th>
                    <td>
                        <input type="text" class="form-control datepicker"  class='form-control col-sm-2' name="date_end" data-date-format="YYYY/MM/DD" value="{{ old('date_end') ? old('date_end') : ($dateEndInput ?? '') }}">
                        <small class="error">{{ $errors->first('date_end') }}</small>
                    </td>
                </tr>
                @if($accessLevel == 'sysadmin')
                    <tr>
                        <th width="15%"><label for="scheme_admins_input">Scheme Admins <i class="fas fa-question-circle popover-dismiss" data-container="body" data-toggle="tooltip" data-placement="right"
                                                                                          title="Provide the email address of those you wish to give administrative privileges within this particular scheme"></i></label></th>
                        <td>
                            <textarea id="scheme_admins_input" name="scheme_admins" hidden></textarea>
                            <div id="scheme_admins_input_container">
                                <div class="input-group" style="padding-bottom: 5px;">
                                    <input id="scheme_admins_selector_input" class="form-control" type="text" placeholder="Email address" style="margin-right: 5px;" onkeydown="onInputListKeyDown(event);" onkeyup="onInputListKeyUp(event, 'scheme_admins_selector_add');">
                                    <span id="scheme_admins_selector_add" onclick="newSchemeAdmin()" class="btn btn-outline-primary">Add</span>
                                </div>
                                <ul id="scheme_admins_list" class="list-group">
                                    @if((old('scheme_admins') && strlen(old('scheme_admins')) > 0) || (isset($schemeAdmins) && strlen($schemeAdmins) > 0))
                                        @foreach(explode(PHP_EOL, old('scheme_admins') ? old('scheme_admins') : $schemeAdmins) as $schemeAdminEmail)
                                            @if(strlen(trim($schemeAdminEmail)) > 0)
                                                <li class="list-group-item {{isset($invalidAdminEmails) && in_array($schemeAdminEmail, $invalidAdminEmails) ? 'list-group-item-danger' : 'list-group-item-info'}}">{{ $schemeAdminEmail }}
                                                    <span class="close" onclick="removeListElement(this, 'scheme_admins_input', '{{$schemeAdminEmail}}')">&times;</span>
                                                </li>
                                            @endif
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                            <small class="error">{{ $errors->first('scheme_admins') }}</small>
                        </td>
                    </tr>
                    @if(!isset($schemeID))
                        <tr>
                            <th width="15%"><label for="question_count_input">Initial Questions <i class="fas fa-question-circle popover-dismiss" data-container="body" data-toggle="tooltip" data-placement="right"
                                                                                                   title="Select how many questions you wish for the questionnaire to have. This can always be changed later."></i></label></th>
                            <td>
                                <select id="question_count_input" name="question_count" class="form-control" title="The number of questions initially in the questionnaire.">
                                    @for($questionCount = $maxQuestions; $questionCount >= 5; $questionCount--)
                                        <option value="{{$questionCount}}"{{ old('question_count') == $questionCount ? ' selected' : '' }}>{{$questionCount}}</option>
                                    @endfor
                                </select>
                                <small class="error">{{ $errors->first('question_count') }}</small>
                            </td>
                        </tr>
                    @endif
                @endif
                </tbody>
            </table>
            @if(isset($rules) && !empty($rules))
                <div>
                    <h4>Rules</h4>
                    <table class="table table-borderless">
                        <tbody>
                        @foreach($rules as $ruleID => $ruleData)
                            @php
                                $ruleData['nameLc'] = str_replace(' ', '_', strtolower($ruleData['name']));
                                $ruleData['oldValue'] = old($ruleData['nameLc']);
                            @endphp
                            @include('forms.rules.rule', $ruleData)
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            <input value="SAVE" class="btn btn-outline-success" type="submit">
            <a href="{{ isset($schemeID) ? route('schemes.show', ['scheme_id' => $schemeID]) : route('schemes.index') }}" class="btn btn-outline-danger" style="float: right;">CANCEL</a>
        </form>
    </div>
@endsection

@section('scripts')
    @if($accessLevel === 'sysadmin')
        <script type="application/javascript">
            function newListItem(textInputElement, listContainerElementId, textAreaElement, listGroupItemClass) {
                var inputText = textInputElement.value;
                var t = document.createTextNode(inputText);
                textInputElement.value = "";

                var li = document.createElement("li");
                li.classList.add('list-group-item', listGroupItemClass);
                li.appendChild(t);

                var span = document.createElement("span");
                var txt = document.createTextNode("\u00D7");
                span.className = "close";
                span.appendChild(txt);
                li.appendChild(span);
                span.onclick = function () {
                    removeListElement(this);
                };

                var ul = document.getElementById(listContainerElementId);
                ul.appendChild(li);

                if (textAreaElement.value.length > 0) {
                    textAreaElement.value += '\n';
                }
                textAreaElement.value += inputText;
            }

            function newDepartment() {
                var departmentsSelectorInput = document.getElementById("departments_selector_input");
                if (departmentsSelectorInput.value.length > 0) {
                    if (validateDepartment(departmentsSelectorInput.value)) {
                        var departmentsInput = document.getElementById("departments_input");
                        if (!departmentsInput.value.includes(departmentsSelectorInput.value)) {
                            newListItem(departmentsSelectorInput, 'departments_list', departmentsInput, 'list-group-item-warning');
                        } else {
                            alert('You have already added that department.');
                        }
                    } else {
                        alert('Please enter a valid department name.');
                    }
                } else {
                    alert('Please enter a department name.');
                }
            }

            function newSchemeAdmin() {
                var schemeAdminsSelectorInput = document.getElementById("scheme_admins_selector_input");
                if (schemeAdminsSelectorInput.value.length > 0) {
                    if (validateEmail(schemeAdminsSelectorInput.value)) {
                        var schemeAdminsInput = document.getElementById("scheme_admins_input");
                        if (!schemeAdminsInput.value.includes(schemeAdminsSelectorInput.value)) {
                            newListItem(schemeAdminsSelectorInput, 'scheme_admins_list', schemeAdminsInput, 'list-group-item-info');
                        } else {
                            alert('You have already added that user.');
                        }
                    } else {
                        alert('Invalid user email address!');
                    }
                } else {
                    alert('Please enter an email address.');
                }
            }

            function onInputListKeyDown(e) {
                e = e || window.event;
                if (e.keyCode === 13) {
                    e.preventDefault();
                }
            }

            function onInputListKeyUp(e, addButtonId) {
                e = e || window.event;
                if (e.keyCode === 13) {
                    document.getElementById(addButtonId).click();
                }
            }

            function removeListElement(spanElement, textAreaInputId, text) {
                var listElement = spanElement.parentElement;
                listElement.remove();

                var textAreaInput = document.getElementById(textAreaInputId);
                var textAreaValue = textAreaInput.value;
                if (textAreaValue != null) {
                    var textAreaItems = textAreaValue.split(/\r?\n/);
                    var newValue = '';
                    for (var i = 0; i < textAreaItems.length; ++i) {
                        var textAreaItem = textAreaItems[i].trim();
                        if (textAreaItem.length > 0 && textAreaItem != text) {
                            if (newValue.length > 0) newValue += '\n';
                            newValue += textAreaItem;
                        }
                    }
                    textAreaInput.value = newValue;
                }
            }

            function validateDepartment(department) {
                var re = /^[a-zA-Z ]*$/;
                return re.test(String(department));
            }

            function validateEmail(email) {
                var re = /^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/; // /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                return re.test(String(email).toLowerCase());
            }

            initialiseListInput(document.getElementById('departments_input'), 'departments_list');
            initialiseListInput(document.getElementById('scheme_admins_input'), 'scheme_admins_list');

            function initialiseListInput(textAreaInput, listElementId) {
                textAreaInput.value = "";

                var listContainerElement = document.getElementById(listElementId);
                var listElements = listContainerElement.getElementsByTagName("li");
                for (var i = 0; i < listElements.length; i++) {
                    var listElement = listElements[i];
                    var adminEmail = listElement.innerHTML.substr(0, listElement.innerHTML.indexOf('<span')).trim();
                    if (adminEmail.length > 0) {
                        textAreaInput.value += adminEmail;
                        if (i !== listElements.length - 1) {
                            textAreaInput.value += '\n';
                        }
                    }
                }
                return true;
            }
        </script>
    @endif

    <script type="application/javascript">
        function updateIcon(iconInput) {
            if (iconInput.files) {
                var iconFile = iconInput.files[0];
                if (iconFile.size < 2097152) {
                    var reader = new FileReader();
                    var icon = document.getElementById("icon");
                    reader.onload = function (e) {
                        var img = new Image();
                        img.onload = function () {
                            if (img.width === 200 && img.height === 200) {
                                icon.src = e.target.result;
                            } else {
                                alert('Please upload an image with resolution 200x200');
                            }
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(iconFile);
                } else {
                    iconInput.value = null;
                    alert('The image file size must be <= 2MB');
                }
            }
        }

        function deleteIcon() {
            var iconInput = document.getElementById("icon_input");
            if (iconInput.value !== '') {
                if (confirm('Are you sure you want to remove that file?')) {
                    iconInput.value = null;

                    var icon = document.getElementById("icon");
                    icon.src = '/storage/schemes/icons/{{ isset($icon) ? $icon : \App\Scheme::getDefaultIcon() }}';
                }
            } else {
                @if(isset($icon))
                if (confirm('Are you sure you want to remove the icon?')) {
                    document.getElementById("delete_icon_form").submit();
                }
                @else
                alert('An icon for this scheme has not been set!');
                @endif
            }
        }
    </script>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection