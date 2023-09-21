@extends ('laraview.layouts.sideNavLayout')

@section('title')
    Feedback status
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '5';
        $active_link = '1';
    @endphp
@endsection

@section('sidebar')
    @include('teacher.sidebar')
@endsection

@section('contentTitle')
    <h3>Feedback status </h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-header">
            <a class="btn btn-dark" href="{{ route('assessment_events.index') }}" role="button">
                <i class="fas fa-backward"></i> Back
            </a>
        </div>

        <div class="card-body">

            <div class="row">
                <div class="col-sm">
                    <div class="callout callout-info">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><span class="font-weight-bold"> Course : </span>
                                {{ $assessment_event->course->name }}
                            </li>
                            <li class="list-group-item"><span class="font-weight-bold"> Teacher : </span>
                                {{ $assessment_event->teacher->name }}</li>
                            <li class="list-group-item"><span class="font-weight-bold"> Start Time: </span>
                                {{ $assessment_event->start_time }}
                            </li>
                            <li class="list-group-item"><span class="font-weight-bold"> Stop Time: </span>
                                {{ $assessment_event->stop_time }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <table id="data_table" class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Student ID</th>
                        <th scope="col">Name </th>
                        <th scope="col">status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assessed as $student)
                        <tr>
                            <td>{{ $student->student_id }}</td>
                            <td>{{ $student->name }}</td>
                            <td>Done</td>
                        </tr>
                    @endforeach
                    @foreach ($not_assessed as $student)
                        <tr>
                            <td>{{ $student->student_id }}</td>
                            <td>{{ $student->name }}</td>
                            <td>Pending</td>
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
