@extends ('laraview.layouts.sideNavLayout')

@section('title')
    Edit Teacher
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '1';
        $active_link = '1';
    @endphp
@endsection

@section('sidebar')
    @include('teacher.sidebar')
@endsection

@section('contentTitle')
    <h3>Edit Teacher</h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-body">

            <p class="text-danger">* required field</p>

            <div class="row">
                <div class="col-sm-6">
                    <form autocomplete="off" id="quickForm" method="POST"
                        action="{{ route('users.update', ['user' => $user]) }}">
                        @csrf
                        @method('put')

                        <!--name-->
                        <div class="form-group">
                            <label for="name"><span class="text-danger">*</span>Name</label>
                            <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" value="{{ $user->name }}" autocomplete="name" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/name-->

                        <!--email-->
                        <div class="form-group">
                            <label for="email"><span class="text-danger">*</span>Email address</label>
                            <input name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" value="{{ $user->email }}" autocomplete="email" required>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/email-->

                        <!--password-->
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input name="password" type="password"
                                class="form-control @error('password') is-invalid @enderror" id="password"
                                placeholder="Password" autocomplete="false">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--password-->

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
