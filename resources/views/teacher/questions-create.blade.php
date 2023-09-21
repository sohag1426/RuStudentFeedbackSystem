@extends ('laraview.layouts.sideNavLayout')

@section('title')
    New Questions
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '4';
        $active_link = '1';
    @endphp
@endsection

@section('sidebar')
    @include('teacher.sidebar')
@endsection

@section('contentTitle')
    <h3>New Question</h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-body">

            <p class="text-danger">* required field</p>

            <form id="quickForm" autocomplete="off" method="POST" action="{{ route('questions.store') }}">

                @csrf

                <!--en-->
                <div class="form-group">
                    <label for="en"><span class="text-danger">*</span>English</label>
                    <input name="en" type="text" class="form-control @error('en') is-invalid @enderror"
                        id="en" value="{{ old('en') }}" required>
                    @error('en')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <!--/en-->

                <!--bn-->
                <div class="form-group">
                    <label for="bn"><span class="text-danger">*</span>Bangla</label>
                    <input name="bn" type="text" class="form-control @error('bn') is-invalid @enderror"
                        id="bn" value="{{ old('bn') }}" required>
                    @error('bn')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <!--/bn-->

                <button type="submit" class="btn btn-dark">Submit</button>
        </div>

    </div>
@endsection

@section('pageJs')
@endsection
