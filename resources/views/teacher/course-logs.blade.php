@extends('laraview.layouts.sideNavLayout')

@section('title')
    {{ $course->code }} Change Logs
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
    <h3>Change Logs: {{ $course->code }} - {{ $course->name }}</h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">
            <a class="btn btn-dark" href="{{ route('courses.index') }}" role="button">
                <i class="fas fa-backward"></i> Back
            </a>
            <a class="btn btn-outline-info" href="{{ route('courses.change-logs') }}" role="button">
                <i class="fas fa-list"></i> All Course Logs
            </a>
        </div>

        <div class="card-body">

            <div class="callout callout-info mb-4">
                <h5><strong>Course:</strong> {{ $course->code }} - {{ $course->name }}</h5>
                <p class="mb-0">
                    <strong>Year:</strong> {{ $course->year?->value ?? $course->year ?? '-' }} |
                    <strong>Semester:</strong> {{ $course->semester?->value ?? $course->semester ?? '-' }}
                </p>
            </div>

            <table id="data_table" class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Topic</th>
                        <th scope="col">Log</th>
                        <th scope="col">User</th>
                        <th scope="col">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td scope="row">{{ $log->id }}</td>
                            <td>{{ $log->topic }}</td>
                            <td>{{ $log->log }}</td>
                            <td>{{ $log->user->name ?? '-' }}</td>
                            <td>{{ $log->created_at?->format('Y-m-d H:i:s') ?? $log->created_at }}</td>
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
