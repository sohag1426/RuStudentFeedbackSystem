@extends ('laraview.layouts.sideNavLayout')

@section('title')
New Officer/Staff
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '6';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('teacher.sidebar')
@endsection

@section('contentTitle')
<h3>New Officer/Staff</h3>
@endsection

@section('content')
<div class="card">

    <div class="card-body">

        <p class="text-danger">* required field</p>

        <div class="row">
            <div class="col-12 col-md-6">
                <form id="quickForm" autocomplete="off" method="POST" action="{{ route('department-manager.store') }}">
                    @csrf

                    <!--internet_id-->
                    <div class="form-group">
                        <label for="internet_id"><span class="text-danger">*</span>Internet Id (Eight Digit)</label>
                        <input name="internet_id" type="text"
                            class="form-control @error('internet_id') is-invalid @enderror" id="internet_id"
                            value="{{ old('internet_id') }}" autocomplete="new-password" required>
                        @error('internet_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <!--/internet_id-->

                    <!--name-->
                    <div class="form-group">
                        <label for="name"><span class="text-danger">*</span>Name</label>
                        <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                            id="name" value="{{ old('name') }}" autocomplete="new-password" required>
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <!--/name-->

                    <!--mobile-->
                    <div class="form-group">
                        <label for="mobile"><span class="text-danger">*</span>Mobile</label>
                        <input name="mobile" type="text" class="form-control @error('mobile') is-invalid @enderror"
                            id="mobile" value="{{ old('mobile') }}" autocomplete="new-password" required>
                        @error('mobile')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <!--/mobile-->

                    <!--email-->
                    <div class="form-group">
                        <label for="email"><span class="text-danger">*</span>Email address</label>
                        <input name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            id="email" value="{{ old('email') }}" autocomplete="new-password" required>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <!--/email-->

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