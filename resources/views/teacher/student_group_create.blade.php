@extends ('laraview.layouts.sideNavLayout')

@section('title')
    New Student Group
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '3';
        $active_link = '1';
    @endphp
@endsection

@section('sidebar')
    @include('teacher.sidebar')
@endsection

@section('contentTitle')
    <h3>New Student Group</h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-body">

            <p class="text-danger">* required field</p>

            <div class="row">

                <div class="col-sm-6">

                    <form id="quickForm" autocomplete="off" method="POST" action="{{ route('student_groups.store') }}">

                        @csrf

                        <!--name-->
                        <div class="form-group">
                            <label for="name"><span class="text-danger">*</span>Group Name</label>
                            <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/name-->

                        <!--session-->
                        <div class="form-group">
                            <label for="session"><span class="text-danger">*</span>Session</label>
                            <select name="session" id="session" class="form-control @error('session') is-invalid @enderror" required>
                                <option value="">Select Session</option>
                                @foreach ($sessions ?? \App\Services\SessionService::getSessions() as $sessionOption)
                                    <option value="{{ $sessionOption }}" {{ old('session') == $sessionOption ? 'selected' : '' }}>
                                        {{ $sessionOption }}
                                    </option>
                                @endforeach
                            </select>
                            @error('session')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/session-->

                        <!--year-->
                        <div class="form-group">
                            <label for="year"><span class="text-danger">*</span>Year</label>
                            <select name="year" id="year" class="form-control @error('year') is-invalid @enderror" required>
                                <option value="">Select Year</option>
                                @foreach ($years ?? \App\Enums\Year::cases() as $yearOption)
                                    <option value="{{ $yearOption->value }}" {{ old('year') == $yearOption->value ? 'selected' : '' }}>
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
                            <label for="semester"><span class="text-danger">*</span>Semester</label>
                            <select name="semester" id="semester" class="form-control @error('semester') is-invalid @enderror" required>
                                <option value="">Select Semester</option>
                                @foreach ($semesters ?? \App\Enums\Semester::cases() as $semesterOption)
                                    <option value="{{ $semesterOption->value }}" {{ old('semester') == $semesterOption->value ? 'selected' : '' }}>
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
