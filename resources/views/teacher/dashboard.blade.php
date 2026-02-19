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

    <div class="card-header">
        <h3 class="card-title">Login History</h3>
    </div>

    <div class="card-body">

        <table id="data_table" class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Topic</th>
                    <th scope="col">Log</th>
                    <th scope="col">User</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                <tr>
                    <td scope="row">{{ $log->id }}</td>
                    <td>{{ $log->topic }}</td>
                    <td>{{ $log->log }}</td>
                    <td>{{ $log->user->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
@endsection

@section('pageJs')
@endsection