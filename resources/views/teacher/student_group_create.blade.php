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
