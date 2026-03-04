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

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card-body">

            <livewire:admin-dashboard-filter :selected-department="$selectedDepartment" :departments="$departments" />

            <table id="phpPaging" class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Teacher</th>
                        <th scope="col">Department</th>
                        <th scope="col">Course</th>
                        <th scope="col">Start Time</th>
                        <th scope="col">Stop Time</th>
                        <th scope="col">Feedback %</th>
                        <th scope="col">Score</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assessment_events as $assessment_event)
                        <tr>
                            <td scope="row">{{ $assessment_event->id }}</td>
                            <td>{{ $assessment_event->teacher->name }}</td>
                            <td>{{ $assessment_event->department->en_name }}</td>
                            <td>{{ $assessment_event->course->name }} ({{ $assessment_event->course->code }})</td>
                            <td>{{ $assessment_event->start_time }}</td>
                            <td>{{ $assessment_event->stop_time }}</td>
                            <td>{{ $assessment_event->feedback_percentage }} %</td>
                            @if (auth('admin')->user()->role == 'SuperAdmin')
                                <td>{{ $assessment_event->score }}</td>
                            @else
                                <td>***</td>
                            @endif
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
