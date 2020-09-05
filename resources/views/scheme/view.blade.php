@extends('layouts.app_layout')

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="fas fa-table"></i>
                <a href="{{route('schemes.index')}}">Schemes</a>
            </li>
            <li class="breadcrumb-item active">
                <i class="fas fa-eye"></i> {{$name}}
            </li>
        </ol>
        @if(isset($success))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ $success }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if(isset($info))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ $info }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
    </div>

    <div class="container-fluid">
        <h1>Scheme</h1>
        @if(isset($icon))
            <div class="text-center" style="padding-bottom: 10px;">
                <div class="mx-auto" style="width: 200px; height: 200px;">
                    <img id="icon" class="img-fluid" src="/storage/schemes/icons/{{ $icon }}" alt="Scheme icon">
                </div>
            </div>
        @endif
        <table class="table">
            <tbody>
            <tr>
                <th width="20%">Name</th>
                <td>{{ $name }}</td>
            </tr>
            <tr>
                <th>Description</th>
                <td>{{ $description }}</td>
            </tr>
            @if($schemeLevel !== 'user')
                <tr>
                    <th>Type</th>
                    <td>{{ $type }}</td>
                </tr>
            @endif
            @if(isset($departments))
                <tr>
                    <th>Departments</th>
                    <td>
                        <ul style="margin-bottom: 0">
                            @foreach($departments as $department)
                                <li>{{ $department }}</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
            @endif
            @if(isset($joinCodes) && count($joinCodes) > 0)
                <th>Join Codes <i class="fas fa-question-circle popover-dismiss" data-container="body" data-toggle="tooltip" data-placement="right"
                                  title="Provide these codes to anyone you wish to join your scheme, clearly indicating which code is for “Buddies” (mentors), and which code is for “Newbies” (mentees). This will allow them to register onto the scheme and into the correct role. There is also the option to provide a direct link if you prefer, or use both. Simply click on “copy link” and send it to whoever you wish. "></i></th>
                <td>
                    <table class="table table-borderless">
                        <tbody>
                        @foreach($joinCodes as $userTypeID => $joinCodeData)
                            <tr>
                                <th width="10%" style="display: inline-list-item; vertical-align: middle;">{{ $joinCodeData['userType']->getNames()['plural'] }}</th>
                                <td width="5%" style="display: inline-list-item; vertical-align: middle;">
                                    <div class="input-group-text" id="join_code_{{ $userTypeID }}">{{ $joinCodeData['joinCode'] }}</div>
                                </td>
                                <td width="{{ $accessLevel == 'sysadmin' ? '10%' : '85%' }}">
                                    <button class="btn btn-outline-primary" onclick="copyJoinCode('{{ route('schemes.apply_join_code', ['join_code' => $joinCodeData['joinCode']]) }}')">
                                        Copy Join Link
                                    </button>
                                </td>
                                @if($accessLevel == 'sysadmin')
                                    <td width="75%">
                                        <form action="{{ route('schemes.reset_join_code', ['scheme_id' => $schemeID, 'user_type_id' => $joinCodeData['userType']->id]) }}" method="post" onsubmit="return confirm('Are you sure you want to reset the join code?');">
                                            @csrf
                                            <button id="reset_code_{{ $userTypeID }}" class="btn btn-outline-danger">
                                                Reset
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </td>
            @endif
            <tr>
                <th>Start Date</th>
                <td>{{ $dateStart }}</td>
            </tr>
            <tr>
                <th>Deadline Date</th>
                <td>{{ $dateEnd }}</td>
            </tr>
            @if(!empty($schemeAdmins))
                <tr>
                    <th>Scheme Admins</th>
                    <td>
                        <table class="table table-borderless">
                            <tbody>
                            @foreach($schemeAdmins as $schemeAdmin)
                                <tr>
                                    <td width="30%">{{ $schemeAdmin->getFullName() . (isset($schemeAdmin->nickname) ? ' (' . $schemeAdmin->nickname . ')' : '') }}</td>
                                    <td><a href="mailto:{{$schemeAdmin->email}}">{{ $schemeAdmin->email }}</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
        <div class="input-group">
            @if($schemeLevel === 'user')
                <a class="btn btn-outline-success" href="{{ route('schemes.preferences.edit', ['scheme_id' => $schemeID]) }}" style="margin-right: 5px;">PREFERENCES</a>
            @endif
            @if($canEdit === true)
                <a class="btn btn-outline-success" href="{{ route('schemes.edit', ['scheme_id' => $schemeID]) }}" style="margin-right: 5px;">EDIT</a>
                <a class="btn btn-outline-info" href="{{ route('rules.index', ['scheme_id' => $schemeID]) }}" style="margin-right: 5px;">RULES</a>
                @if($typeID === 3 || $accessLevel == 'sysadmin')
                    <a class="btn btn-outline-dark" href="{{ route('schemes.pairs.store', ['scheme_id' => $schemeID]) }}" style="margin-right: 5px;">PAIR</a>
                @endif
                <a class="btn btn-outline-dark" href="{{ route('schemes.pairs.index', ['scheme_id' => $schemeID]) }}" style="margin-right: 5px;">PAIRINGS</a>
                <a class="btn btn-outline-secondary" href="{{ route('schemes.questions.index', ['scheme_id' => $schemeID]) }}" style="margin-right: 5px;">QUESTIONS</a>
            @endif
            @if($accessLevel == 'sysadmin')
                <form action="{{ route('schemes.destroy', ['scheme_id' => $schemeID]) }}" method="post">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger">DELETE</button>
                </form>
            @endif
        </div>

        <a class="btn btn-outline-danger" href="{{ route('schemes.index') }}" style="float: right;">BACK</a>
    </div>
@endsection

@section('scripts')
    <script type="application/javascript">
        // Credits: https://hackernoon.com/copying-text-to-clipboard-with-javascript-df4d4988697f
        function copyJoinCode(joinUrl) {
            const textArea = document.createElement('textarea');
            textArea.value = joinUrl;
            textArea.setAttribute('readonly', '');
            textArea.style.position = 'absolute';
            textArea.style.left = '-9999px';
            document.body.appendChild(textArea);

            const selected =
                document.getSelection().rangeCount > 0        // Check if there is any content selected previously
                    ? document.getSelection().getRangeAt(0)     // Store selection if found
                    : false;

            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);

            if (selected) {                                 // If a selection existed before copying
                document.getSelection().removeAllRanges();    // Unselect everything on the HTML document
                document.getSelection().addRange(selected);   // Restore the original selection
            }
        }
    </script>
@endsection