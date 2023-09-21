@extends ('laraview.layouts.sideNavLayout')

@section('title')
    Dashboard
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '0';
        $active_link = '0';
    @endphp
@endsection

@section('sidebar')
    @include('teacher.sidebar')
@endsection

@section('contentTitle')
    <h3>Dashboard</h3>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            You're logged in!
        </div>
    </div>
@endsection

@section('pageJs')
@endsection
