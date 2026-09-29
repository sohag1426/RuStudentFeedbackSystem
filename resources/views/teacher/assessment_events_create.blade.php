@extends ('laraview.layouts.sideNavLayout')

@section('title')
    New Feedback Event
@endsection

@section('pageCss')
    <style>
        [x-cloak] { display: none !important; }
    </style>
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

                    <form id="quickForm" autocomplete="off" method="POST" action="{{ route('assessment_events.store') }}"
                          x-data="assessmentEventForm()" x-init="init()">

                        @csrf

                        <!--course_id-->
                        <div class="form-group">
                            <label for="course_id"><span class="text-danger">*</span>Course</label>
                            <select class="form-control @error('course_id') is-invalid @enderror"
                                    id="course_id"
                                    name="course_id"
                                    x-model="courseId"
                                    @change="onCourseChange()"
                                    required>
                                <option value="">Please select Course...</option>
                                @forelse ($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }} : {{ $course->code }}
                                        @if ($course->year && $course->semester)
                                            ({{ $course->year->value ?? $course->year }}, {{ $course->semester->value ?? $course->semester }})
                                        @endif
                                    </option>
                                @empty
                                    <option value="" disabled>No eligible courses found. Please update courses first.</option>
                                @endforelse
                            </select>
                            @error('course_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted" x-show="selectedCourse && selectedCourse.year && selectedCourse.semester" x-cloak>
                                Course Term: <strong x-text="selectedCourse.year + ', ' + selectedCourse.semester"></strong>
                            </small>
                            <small class="form-text text-muted">
                                Note: Only courses with assigned Year &amp; Semester are listed. If a course is missing, please update it under <a href="{{ route('courses.index') }}">Courses</a> first.
                            </small>
                        </div>
                        <!--/course_id-->

                        <!--group_id-->
                        <div class="form-group">
                            <label for="group_id"><span class="text-danger">*</span>Student Group</label>
                            <select class="form-control @error('group_id') is-invalid @enderror"
                                    id="group_id"
                                    name="group_id"
                                    required>
                                <option value="">Please select Student Group...</option>
                                @foreach ($groups as $group)
                                    <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                        {{ $group->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('group_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/group_id-->

                        <!--teacher_id-->
                        <div class="form-group">
                            <label for="teacher_id"><span class="text-danger">*</span>Teacher</label>
                            <select class="form-control @error('teacher_id') is-invalid @enderror" id="teacher_id" name="teacher_id" required>
                                <option value="">Please select Teacher...</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id', auth()->id()) == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('teacher_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/teacher_id-->

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
    @vite(['resources/js/app.js'])
    <script>
        function assessmentEventForm() {
            return {
                courses: @json($coursesData),
                courseId: '{{ old('course_id') }}',

                get selectedCourse() {
                    return this.courses.find(c => String(c.id) === String(this.courseId)) || null;
                },

                init() {}
            };
        }

        $(function() {
            $('#datepicker, #datepicker2').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
        });
    </script>
@endsection
