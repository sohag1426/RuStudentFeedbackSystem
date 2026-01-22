@extends ('laraview.layouts.topNavLayout')

@section('title')
    Feedback Form
@endsection

@section('pageCss')
@endsection

@section('company')
    University of Rajshahi
@endsection

@section('topNavbar')
@endsection

@section('contentTitle')
    <h3>Feedback Form</h3>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 col-md-6">
            <div class="callout callout-info">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><span class="font-weight-bold"> Course : </span>
                        {{ $assessment_event->course->name }} ({{ $assessment_event->course->code }})
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
        <div class="col-12 col-md-6">
            <div class="callout callout-info">
                <dl>
                    <dt>
                        Disclaimer:
                    </dt>
                    <dd>
                        Your identity will remain anonymous, and no personal information will be traced by or
                        revealed to anyone, even to the admin of the system. Combining all student's feedback, only
                        an aggregated report will be generated for the purpose of improving the overall educational
                        experience.
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="callout callout-warning">
                <span class="font-weight-bold"> Rating Scale: </span>
                5 - Excellent (চমৎকার), 4 - Very Good (খুব ভালো), 3 - Good (ভালো), 2 - Fair (মোটামুটি), 1 - Poor (খারাপ)
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="callout callout-warning">
                Please be honest, frank and constructive while providing your feedback.
                (আপনার মতামত প্রদানের সময় দয়া করে সৎ, খোলামেলা এবং গঠনমূলক থাকুন/হউন ।)
            </div>
        </div>
    </div>

    <form method="POST"
        action="{{ route('assessment_event_students.assessment_events.update', ['assessment_event' => $assessment_event, 'assessment_event_student' => $assessment_event_student]) }}">

        @csrf

        @method('PUT')

        @foreach ($questions->groupBy('questions_group_id') as $questions_group_id => $group_questions)
            <div class="card card-outline card-dark">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        {{ $questions_group_id }} .
                        <span style="font-size: 18px">
                            {{ $questions_groups->where('id', $questions_group_id)->first()->en_name }} </span>
                        <span style="font-size: 15px">
                            ({{ $questions_groups->where('id', $questions_group_id)->first()->bn_name }})
                        </span>
                    </h3>
                </div>
                <div class="card-body">
                    @foreach ($group_questions as $question)
                        <p>
                            <span class="font-weight-bold ml-2"> {{ $question->question_no }} </span>
                            <span style="font-size: 16px"> {{ $question->en }} </span>
                            <span style="font-size: 13px"> ({{ $question->bn }}) </span>
                        </p>
                        <div class="ml-4">
                            @for ($i = $highest_score; $i >= 1; $i--)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="{{ $question->id }}"
                                        id="inlineRadio-{{ $question->id }}-{{ $i }}"
                                        value="{{ $i }}" required>
                                    <label class="form-check-label"
                                        for="inlineRadio-{{ $question->id }}-{{ $i }}">{{ $i }}</label>
                                </div>
                            @endfor
                        </div>
                        <hr>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="card card-outline card-dark">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <span style="font-size: 18px"> 5. Recommendations/Overall Comments </span>
                    <span style="font-size: 15px"> (প্রস্তাবনা/সামগ্রিক মন্তব্য) </span>
                </h3>
            </div>
            <div class="form-group ml-4">
                <label for="Textarea1">
                    <span style="font-size: 16px"> a) The course could have been improved by </span>
                    <span style="font-size: 13px"> (নিম্নলিখিতভাবে এই কোর্সের উন্নয়ন করা যেতে পারে): </span>
                </label>
                <textarea class="form-control" id="Textarea1" rows="3" name="comment" maxlength="250"></textarea>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mt-2">Submit</button>
            </div>
        </div>

    </form>
@endsection

@section('pageJs')
@endsection
