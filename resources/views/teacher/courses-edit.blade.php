@extends ('laraview.layouts.sideNavLayout')

@section('title')
    Edit Course
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
    <h3>Edit Course</h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-body">

            <p class="text-danger">* required field</p>

            <div class="row">

                <div class="col-sm-6">

                    <form id="quickForm" autocomplete="off" method="POST"
                        action="{{ route('courses.update', ['course' => $course]) }}">

                        @csrf

                        @method('PUT')

                        <!--code-->
                        <div class="form-group">
                            <label for="code">
                                @can('updateCode', $course)
                                    <span class="text-danger">*</span>
                                @endcan
                                Course Code
                                @cannot('updateCode', $course)
                                    <small class="text-muted">(Locked)</small>
                                @endcannot
                            </label>
                            <input name="code" type="text" class="form-control @error('code') is-invalid @enderror"
                                id="code" value="{{ old('code', $course->code) }}"
                                @cannot('updateCode', $course) disabled @endcannot
                                @can('updateCode', $course) required @endcan>
                            @error('code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/code-->

                        <!--name-->
                        <div class="form-group">
                            <label for="name">
                                @can('updateName', $course)
                                    <span class="text-danger">*</span>
                                @endcan
                                Course Name
                                @cannot('updateName', $course)
                                    <small class="text-muted">(Locked)</small>
                                @endcannot
                            </label>
                            <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" value="{{ old('name', $course->name) }}"
                                @cannot('updateName', $course) disabled @endcannot
                                @can('updateName', $course) required @endcan>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/name-->

                        <!--year-->
                        <div class="form-group">
                            <label for="year">
                                @can('updateYear', $course)
                                    <span class="text-danger">*</span>
                                @endcan
                                Year
                                @cannot('updateYear', $course)
                                    <small class="text-muted">(Locked)</small>
                                @endcannot
                            </label>
                            <select name="year" id="year" class="form-control @error('year') is-invalid @enderror"
                                @cannot('updateYear', $course) disabled @endcannot
                                @can('updateYear', $course) required @endcan>
                                <option value="">Select Year</option>
                                @foreach ($years ?? \App\Enums\Year::cases() as $yearOption)
                                    <option value="{{ $yearOption->value }}" {{ old('year', $course->year?->value ?? $course->year) == $yearOption->value ? 'selected' : '' }}>
                                        {{ $yearOption->value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('year')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/year-->

                        <!--semester-->
                        <div class="form-group">
                            <label for="semester">
                                @can('updateSemester', $course)
                                    <span class="text-danger">*</span>
                                @endcan
                                Semester
                                @cannot('updateSemester', $course)
                                    <small class="text-muted">(Locked)</small>
                                @endcannot
                            </label>
                            <select name="semester" id="semester" class="form-control @error('semester') is-invalid @enderror"
                                @cannot('updateSemester', $course) disabled @endcannot
                                @can('updateSemester', $course) required @endcan>
                                <option value="">Select Semester</option>
                                @foreach ($semesters ?? \App\Enums\Semester::cases() as $semesterOption)
                                    <option value="{{ $semesterOption->value }}" {{ old('semester', $course->semester?->value ?? $course->semester) == $semesterOption->value ? 'selected' : '' }}>
                                        {{ $semesterOption->value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('semester')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/semester-->

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
@endsection
