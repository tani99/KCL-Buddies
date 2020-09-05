@extends('layouts.app_layout')

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <i class="fas fa-table"></i> Schemes
            </li>
        </ol>
        @if(isset($success))
            <div class="alert alert-success">
                {{ $success }}
            </div>
        @endif
        @if(isset($info))
            <div class="alert alert-info">
                {{ $info }}
            </div>
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="alert alert-danger">
                    {{ $error }}
                </div>
            @endforeach
        @endif
    </div>

    <div class="container-fluid">
        <h4>Schemes</h4>
        <div class="col-xs-1" style="margin-right: 5px;">
            @if($accessLevel !== 'sysadmin')
                <form action="{{ route('schemes.apply') }}" method="post">
                    @csrf
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <label class="input-group-text" id="prepend" for="join_code_input">JOIN CODE: <i class="fas fa-question-circle popover-dismiss" data-container="body" data-toggle="tooltip" data-placement="right"
                                                                                                             title="Type in the join code provided to you by your department/group leader. This will allow you to register yourself onto the scheme. Your group leader might have opted to provide a direct join link instead. Please contact them if you are unable to find either of these."></i></label>
                        </div>
                        <input class="form-control" id="join_code_input" name="join_code" type="text" minlength="5" maxlength="5" aria-describedby="prepend">
                        <button class="btn btn-outline-success" type="submit" style="margin-left: 10px">JOIN</button>
                    </div>
                </form>
                <br>
            @else
                <a class="btn btn-outline-success" href="{{ route('schemes.create') }}" style="float: right;">NEW SCHEME</a>
                <br>
                <br>
            @endif
        </div>
        <div class="no-more-tables">
            <table class="table table-bordered">
                <thead class="thead-dark cf">
                <tr>
                    <th width="17%">Name</th>
                    <th width="{{$accessLevel === 'sysadmin' ? 35 : 40}}%">Description</th>
                    @if($accessLevel === 'sysadmin')
                        <th width="5%">Type</th>
                    @endif
                    <th width="11%">Departments</th>
                    <th width="6%">Start Date</th>
                    <th width="6%">End Date</th>
                    <th width="20%">Actions</th>
                </tr>
                </thead>
                <tbody>
                @if(!empty($schemes))
                    @foreach($schemes as $schemeArray)
                        @php
                            $scheme = $schemeArray[0];
                            $schemeData = $schemeArray[1];
                        @endphp
                        <tr>
                            <td data-title="Name">{{$scheme->name}}</td>
                            <td data-title="Description">{{$scheme->description}}</td>
                            @if($accessLevel === 'sysadmin')
                                <td data-title="Type">{{$schemeTypes[$scheme->type_id]->name}}</td>
                            @endif
                            <td data-title="Departments">
                                @if(isset($scheme->departments))
                                    <ul style="padding-left: 20px; margin-bottom: 0">
                                        @foreach($scheme->getDepartments() as $department)
                                            <li>{{$department}}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                            <td data-title="Start Date">{{$scheme->date_start}}</td>
                            <td data-title="End Date">{{$scheme->date_end}}</td>
                            <td data-title="Actions">
                                <div class="btn-group">
                                    <a class="btn btn-outline-info" href="{{ route('schemes.show', ['scheme_id' => $scheme->id]) }}" style="margin-right: 5px;">VIEW</a>
                                    @if(isset($schemeData['paired']) && $schemeData['paired'] === true)
                                        <a class="btn btn-outline-secondary" href="{{ route('schemes.pairs.user_index', ['scheme_id' => $scheme->id]) }}" style="margin-right: 5px;">GROUP</a>
                                    @endif
                                    @if($schemeData['canEdit'] === true)
                                        <a class="btn btn-outline-success" href="{{ route('schemes.edit', ['scheme_id' => $scheme->id]) }}" style="margin-right: 5px;">EDIT</a>
                                    @endif
                                </div>
                                @if($schemeData['canViewUsers'] === true)
                                    <hr>
                                    <div class="btn-group">
                                        <a class="btn btn-outline-dark" href="{{ route('schemes.users.index', ['scheme_id' => $scheme->id]) }}" style="margin-right: 5px;">USERS</a>
                                        @if($accessLevel == 'sysadmin')
                                            <form action="{{ route('schemes.destroy', ['scheme_id' => $scheme->id]) }}" method="post" onsubmit="return confirmDeletion();">
                                                @method('DELETE')
                                                @csrf
                                                <button class="btn btn-outline-danger">DELETE</button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>

                    @endforeach
                @else
                    <tr>
                        <td class="text-center" colspan="7">
                            @if($accessLevel === 'sysadmin')
                                There are no schemes.
                            @else
                                You have not joined any schemes.
                            @endif
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection

@if($accessLevel == 'sysadmin')
@section('scripts')
    <script type="application/javascript">
        function confirmDeletion() {
            return confirm('Are you sure you want to delete that scheme?');
        }
    </script>
@endsection
@endif