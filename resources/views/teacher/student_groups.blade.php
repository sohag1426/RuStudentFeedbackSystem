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
    <!--New student_group-->
    <li class="nav-item">
        <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('student_groups.create') }}">
            <i class="fas fa-plus"></i>
            Create Student Group
        </a>
    </li>
    <!--/New student_group-->
</ul>
@endsection

@section('content')
<div class="card">

    <!--modal -->
    <div class="modal" tabindex="-1" role="dialog" id="modal-default">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="modal-title" class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body overflow-auto" id="ModalBody">
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /modal-content -->
        </div>
        <!-- /modal-dialog -->
    </div>
    <!-- /modal -->

    <div class="card-body">

        <table id="data_table" class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Group Name</th>
                    <th scope="col">Total Student</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($student_groups as $student_group)
                <tr>
                    <td scope="row">{{ $student_group->id }}</td>
                    <td>{{ $student_group->name }}</td>
                    <td>{{ $student_group->members->count() }}</td>
                    <td>
                        {{-- Edit --}}
                        <a class="btn btn-outline-info btn-sm mb-2"
                            href="{{ route('student_groups.edit', ['student_group' => $student_group]) }}">
                            <i class="fas fa-edit"></i>
                            Edit
                        </a>
                        {{-- Edit --}}

                        {{-- Students --}}
                        <a class="btn btn-outline-info btn-sm mb-2"
                            href="{{ route('student_groups.student_group_members.index', ['student_group' => $student_group]) }}">
                            <i class="fas fa-expand"></i>
                            Students
                        </a>
                        {{-- Students --}}

                        {{-- Add Students --}}
                        <a class="btn btn-outline-info btn-sm mb-2"
                            href="{{ route('student_groups.student_group_members.create', ['student_group' => $student_group]) }}">
                            <i class="fas fa-plus"></i>
                            Add Students
                        </a>
                        {{-- Add Students --}}

                        @can('delete', $student_group)
                        <form method="POST"
                            action="{{ route('student_groups.destroy', ['student_group' => $student_group]) }}"
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