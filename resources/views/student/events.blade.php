@extends ('laraview.layouts.topNavLayout')

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
    <h3>Courses that are available for feedback</h3>
@endsection

@section('content')
    @foreach ($assessable_events as $assessment_event)
        <div class="card" style="width: 20rem;">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Course: </strong> {{ $assessment_event->course->name }}</li>
                    <li class="list-group-item"><strong>Teacher: </strong> {{ $assessment_event->teacher->name }}</li>
                    <li class="list-group-item"><strong> Start Time: </strong> {{ $assessment_event->start_time }}</li>
                    <li class="list-group-item"><strong> Stop Time: </strong> {{ $assessment_event->stop_time }}</li>
                    <li class="list-group-item"></li>
                </ul>
                <a href="{{ route('assessment_event_students.assessment_events.edit', ['assessment_event' => $assessment_event, 'assessment_event_student' => $assessment_event_student]) }}"
                    class="card-link">Click here to complete the feedback</a>
            </div>
        </div>
    @endforeach
@endsection

@section('pageJs')
@endsection
