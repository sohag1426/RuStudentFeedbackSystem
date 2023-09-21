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
                            <label for="code"><span class="text-danger">*</span>Course Code</label>
                            <input name="code" type="text" class="form-control @error('code') is-invalid @enderror"
                                id="code" value="{{ $course->code }}" required>
                            @error('code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/code-->

                        <!--name-->
                        <div class="form-group">
                            <label for="name"><span class="text-danger">*</span>Course Name</label>
                            <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" value="{{ $course->name }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/name-->

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
