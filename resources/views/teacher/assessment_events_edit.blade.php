@extends ('laraview.layouts.sideNavLayout')

@section('title')
    Edit Feedback Event
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
    <h3>Edit Feedback Event</h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-header">
            <a class="btn btn-dark" href="{{ route('assessment_events.index') }}" role="button">
                <i class="fas fa-backward"></i> Back
            </a>
        </div>

        <div class="card-body">

            <p class="text-danger">* required field</p>

            <div class="row">

                <div class="col-sm-6">

                    <form id="quickForm" autocomplete="off" method="POST"
                        action="{{ route('assessment_events.update', ['assessment_event' => $assessment_event]) }}">

                        @csrf

                        @method('put')

                        <!--teacher_id-->
                        <div class="form-group">
                            <label for="teacher_id">Teacher</label>
                            <select class="form-control" id="teacher_id" name="teacher_id" required>
                                <option value="{{ $assessment_event->teacher_id }}">{{ $assessment_event->teacher->name }}
                                </option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!--/teacher_id-->

                        <!--course_id-->
                        <div class="form-group">
                            <label for="course_id">Course</label>
                            <select class="form-control" id="course_id" name="course_id" required>
                                <option value="{{ $assessment_event->course_id }}">{{ $assessment_event->course->name }}
                                </option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!--/course_id-->

                        <!--group_id-->
                        <div class="form-group">
                            <label for="group_id">Student Group</label>
                            <select class="form-control" id="group_id" name="group_id" required>
                                <option value="{{ $assessment_event->group_id }}">{{ $assessment_event->group->name }}
                                </option>
                                @foreach ($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!--/group_id-->

                        <div class="form-row">

                            <!--start_date-->
                            <div class='form-group col-md-6'>
                                <label for='datepicker'>Start Date</label>
                                <input type='text' name='start_date' id='datepicker' class='form-control'
                                    value="{{ $assessment_event->start_date }}" required>
                            </div>
                            <!--/start_date-->

                            {{-- start_hour --}}
                            <div class="form-group col-md-3">
                                <label for="start_hour">Start Hour</label>
                                <select name="start_hour" id="start_hour" class="form-control" required>
                                    <option selected>{{ $assessment_event->start_hour }}</option>
                                    @for ($i = 8; $i < 20; $i++)
                                        <option>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            {{-- start_hour --}}

                            {{-- start_minute --}}
                            <div class="form-group col-md-3">
                                <label for="start_minute">Start Minute</label>
                                <select name="start_minute" id="start_minute" class="form-control" required>
                                    <option selected>{{ $assessment_event->start_minute }}</option>
                                    @for ($i = 0; $i < 60; $i = $i + 5)
                                        <option>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            {{-- start_minute --}}

                        </div>

                        <div class="form-row">

                            <!--stop_date-->
                            <div class='form-group col-md-6'>
                                <label for='datepicker2'>Stop Date</label>
                                <input type='text' name='stop_date' id='datepicker2' class='form-control'
                                    value="{{ $assessment_event->stop_date }}" required>
                            </div>
                            <!--/stop_date-->

                            {{-- stop_hour --}}
                            <div class="form-group col-md-3">
                                <label for="stop_hour">Stop Hour</label>
                                <select name="stop_hour" id="stop_hour" class="form-control" required>
                                    <option selected>{{ $assessment_event->stop_hour }}</option>
                                    @for ($i = 8; $i < 20; $i++)
                                        <option>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            {{-- stop_hour --}}

                            {{-- stop_minute --}}
                            <div class="form-group col-md-3">
                                <label for="stop_minute">Stop Minute</label>
                                <select name="stop_minute" id="stop_minute" class="form-control" required>
                                    <option selected>{{ $assessment_event->stop_minute }}</option>
                                    @for ($i = 0; $i < 60; $i = $i + 5)
                                        <option>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            {{-- stop_minute --}}

                        </div>

                        <button type="submit" class="btn btn-dark">Submit</button>

                    </form>

                </div>
                <!--/col-sm-6-->

            </div>
            <!--/row-->

        </div>
        <!--/card-body-->

    </div>
@endsection

@section('pageJs')
    <script>
        $(function() {
            $('#datepicker').datepicker({
                autoclose: !0
            });
        });

        $(function() {
            $('#datepicker2').datepicker({
                autoclose: !0
            });
        });
    </script>
@endsection
