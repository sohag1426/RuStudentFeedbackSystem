@extends('laraview.layouts.sideNavLayout')

@section('title')
    Course Change Logs
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
    <h3>Course Change Logs</h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-header">
            <a class="btn btn-dark" href="{{ route('courses.index') }}" role="button">
                <i class="fas fa-backward"></i> Back
            </a>
        </div>

        <div class="card-body">

            <table id="data_table" class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Course</th>
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
                            <td>
                                @if ($log->model)
                                    {{ $log->model->code }} - {{ $log->model->name }}
                                @else
                                    -
                                @endif
                            </td>
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
