@extends ('laraview.layouts.sideNavLayout')

@section('title')
    Feedback students
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
    <ul class="nav flex-column flex-sm-row ml-4">
        <li class="nav-item mr-2">
            <h3>Students</h3>
        </li>
        <!--New student_group-->
        <li class="nav-item">
            <a class="btn btn-outline-success my-2 my-sm-0"
                href="{{ route('assessment_events.assessment_event_students.create', ['assessment_event' => $assessment_event]) }}">
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
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                        <tr>
                            <td>{{ $student->student_id }}</td>
                            <td>{{ $student->name }}</td>
                            <td>
                                <form method="POST"
                                    action="{{ route('assessment_events.assessment_event_students.destroy', ['assessment_event' => $student->event_id, 'assessment_event_student' => $student->id]) }}"
                                    onsubmit="return confirm('Are you sure you want to remove the item?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">DELETE</button>
                                </form>
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
