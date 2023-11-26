@extends('laraview.layouts.topNavLayout')

@section('title')
    Student Feedback
@endsection

@section('pageCss')
@endsection

@section('company')
    University of Rajshahi
@endsection

@section('topNavbar')
@endsection

@section('contentTitle')
    <nav class="navbar navbar-light bg-light">
        <form class="form-inline" method="POST"
            action="{{ route('assessment_event_students.logout.store', ['assessment_event_student' => $assessment_event_student]) }}">
            @csrf
            <button class="btn btn-outline-dark my-2 my-sm-0" type="submit">Logout <i
                    class="fas fa-sign-out-alt"></i></button>
        </form>
    </nav>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title font-weight-bold"> Courses that are open for feedback : </h3>
        </div>
        <div class="card-body">
            @if ($assessable_events->get('notYetSubmitted')->count() == 0)
                <div class="callout callout-info">
                    Currently, there are no courses that are accepting feedback.
                </div>
            @endif
            <div class="row">
                @foreach ($assessable_events->get('notYetSubmitted') as $assessment_event)
                    <div class="card" style="width: 20rem;">
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Course: </strong> {{ $assessment_event->course->name }}
                                </li>
                                <li class="list-group-item"><strong>Teacher: </strong>
                                    {{ $assessment_event->teacher->name }}
                                </li>
                                <li class="list-group-item"><strong> Start Time: </strong>
                                    {{ $assessment_event->start_time }}
                                </li>
                                <li class="list-group-item"><strong> Stop Time: </strong> {{ $assessment_event->stop_time }}
                                </li>
                                <li class="list-group-item"></li>
                            </ul>
                            <a href="{{ route('assessment_event_students.assessment_events.edit', ['assessment_event' => $assessment_event, 'assessment_event_student' => $assessment_event_student]) }}"
                                class="card-link">Click here to complete the feedback</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">The courses that you have already given feedback on :</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Course</th>
                        <th scope="col">Teacher</th>
                        <th scope="col">Start Time</th>
                        <th scope="col">Stop Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assessable_events->get('submitted') as $submitted_event)
                        <tr>
                            <th scope="row">{{ $submitted_event->course->name }}</th>
                            <td>{{ $submitted_event->teacher->name }}</td>
                            <td>{{ $submitted_event->start_time }}</td>
                            <td>{{ $submitted_event->stop_time }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('pageJs')
@endsection
