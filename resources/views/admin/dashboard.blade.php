@extends('laraview.layouts.sideNavLayout')

@section('title')
    Dashboard
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '0';
        $active_link = '0';
    @endphp
@endsection

@section('sidebar')
    @include('admin.sidebar')
@endsection

@section('contentTitle')
    <h3>Dashboard</h3>
@endsection

@section('content')
    <div class="card">

        {{-- @Filter --}}
        <form class="d-flex align-content-start flex-wrap" action="{{ route('admin-dashboard') }}" method="get">

            {{-- department_id --}}
            <div class="form-group col-md-6">
                <select name="department_id" id="department_id" class="form-control">
                    <option value='{{ $selectedDepartment->id }}'>{{ $selectedDepartment->en_name }}</option>
                    @foreach ($departments->sortBy('en_name') as $department)
                        <option value="{{ $department->id }}">
                            {{ $department->en_name }}
                            (Feedback Event Count: {{ $department->events()->count() }})
                        </option>
                    @endforeach
                </select>
            </div>
            {{-- department_id --}}

            <div class="form-group col-md-2">
                <button type="submit" class="btn btn-dark">
                    FILTER
                </button>
            </div>

        </form>

        {{-- @endFilter --}}

        <div class="card-body">

            <table id="phpPaging" class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Teacher</th>
                        <th scope="col">Department</th>
                        <th scope="col">Course</th>
                        <th scope="col">Start Time</th>
                        <th scope="col">Stop Time</th>
                        <th scope="col">Score</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assessment_events as $assessment_event)
                        <tr>
                            <td scope="row">{{ $loop->iteration }}</td>
                            <td>{{ $assessment_event->teacher->name }}</td>
                            <td>{{ $assessment_event->department->en_name }}</td>
                            <td>{{ $assessment_event->course->name }} ({{ $assessment_event->course->code }})</td>
                            <td>{{ $assessment_event->start_time }}</td>
                            <td>{{ $assessment_event->stop_time }}</td>
                            <td>***</td>
                            {{-- <td>{{ $assessment_event->score }}</td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
        <!--/card body-->

        <div class="card-footer">
            <div class="row">

                <div class="col-sm-2">
                    Total Entries: {{ $assessment_events->total() }}
                </div>

                <div class="col-sm-6">
                    {{ $assessment_events->links() }}
                </div>

            </div>
        </div>

    </div>
@endsection

@section('pageJs')
@endsection
