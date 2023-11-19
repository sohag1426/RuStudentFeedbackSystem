@extends ('laraview.layouts.sideNavLayout')

@section('title')
    New Feedback Event
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
    <h3>New Feedback Event</h3>
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

                    <form id="quickForm" autocomplete="off" method="POST" action="{{ route('assessment_events.store') }}">

                        @csrf

                        <!--teacher_id-->
                        <div class="form-group">
                            <label for="teacher_id">Teacher</label>
                            <select class="form-control" id="teacher_id" name="teacher_id" required>
                                <option value="{{ auth('web')->user()->id }}" selected>{{ auth('web')->user()->name }}
                                </option>
                            </select>
                        </div>
                        <!--/teacher_id-->

                        <!--course_id-->
                        <div class="form-group">
                            <label for="course_id">Course</label>
                            <select class="form-control" id="course_id" name="course_id" required>
                                <option value="">Please select...</option>
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
                                <option value="">Please select...</option>
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
                                    value="{{ date('m/d/Y') }}" required>
                            </div>
                            <!--/start_date-->

                            {{-- start_hour --}}
                            <div class="form-group col-md-3">
                                <label for="start_hour">Start Hour</label>
                                <select name="start_hour" id="start_hour" class="form-control" required>
                                    <option selected>Choose...</option>
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
                                    <option selected>Choose...</option>
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
                                    value="{{ date('m/d/Y') }}" required>
                            </div>
                            <!--/stop_date-->

                            {{-- stop_hour --}}
                            <div class="form-group col-md-3">
                                <label for="stop_hour">Stop Hour</label>
                                <select name="stop_hour" id="stop_hour" class="form-control" required>
                                    <option selected>Choose...</option>
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
                                    <option selected>Choose...</option>
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
