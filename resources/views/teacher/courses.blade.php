@extends('laraview.layouts.sideNavLayout')

@section('title')
    courses
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '2';
        $active_link = '1';
    @endphp
@endsection

@section('sidebar')
    @include('teacher.sidebar')
@endsection

@section('contentTitle')
    <ul class="nav flex-column flex-sm-row ml-4">
        <!--New courses-->
        <li class="nav-item">
            <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('courses.create') }}">
                <i class="fas fa-plus"></i>
                New Course
            </a>
        </li>
        <!--/New courses-->
    </ul>
@endsection

@section('content')
    <div class="card">

        <div class="card-body">

            <table id="data_table" class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Course Code</th>
                        <th scope="col">Course Name</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($courses as $course)
                        <tr>
                            <td scope="row">{{ $course->id }}</td>
                            <td>{{ $course->code }}</td>
                            <td>{{ $course->name }}</td>
                            <td>
                                {{-- Edit --}}
                                @can('update', $course)
                                    <a class="btn btn-outline-info btn-sm mb-2"
                                        href="{{ route('courses.edit', ['course' => $course]) }}">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </a>
                                @endcan
                                {{-- Edit --}}
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
