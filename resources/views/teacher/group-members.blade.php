@extends ('laraview.layouts.sideNavLayout')

@section('title')
    Students
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '3';
        $active_link = '1';
    @endphp
@endsection

@section('sidebar')
    @include('teacher.sidebar')
@endsection

@section('contentTitle')
    <ul class="nav flex-column flex-sm-row ml-4">
        <li class="nav-item mr-2">
            <h3>Students</h3>
        </li>
        <!--New student_group-->
        <li class="nav-item">
            <a class="btn btn-outline-success my-2 my-sm-0"
                href="{{ route('student_groups.student_group_members.create', ['student_group' => $student_group]) }}">
                <i class="fas fa-plus"></i>
                Add Students
            </a>
        </li>
        <!--/New student_group-->
    </ul>
@endsection

@section('content')
    <div class="card">

        <div class="card-header">
            <a class="btn btn-dark" href="{{ route('student_groups.index') }}" role="button">
                <i class="fas fa-backward"></i> Back
            </a>
        </div>

        <div class="card-body">

            <div class="row">
                <div class="col-sm">
                    <div class="callout callout-info">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><span class="font-weight-bold"> Group Name : </span>
                                {{ $student_group->name }}
                            </li>
                            <li class="list-group-item"><span class="font-weight-bold"> Total Students : </span>
                                {{ $student_group_members->count() }} </li>
                        </ul>
                    </div>
                </div>
            </div>

            <table id="data_table" class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Student ID</th>
                        <th scope="col">Student Name</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($student_group_members as $student_group_member)
                        <tr>
                            <td scope="row">{{ $student_group_member->id }}</td>
                            <td>{{ $student_group_member->student_id }}</td>
                            <td>{{ $student_group_member->name }}</td>
                            <td>
                                @can('delete', $student_group_member)
                                    <form method="POST"
                                        action="{{ route('student_group_members.destroy', ['student_group_member' => $student_group_member]) }}"
                                        onsubmit="return confirm('Are you sure you want to remove the item?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">DELETE</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
        <!--/card body-->

    </div>
@endsection

@section('pageJs')
@endsection
