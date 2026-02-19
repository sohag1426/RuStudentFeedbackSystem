@extends('laraview.layouts.sideNavLayout')

@section('title')
Login History
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '1';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('contentTitle')
<h3>Login History</h3>
@endsection

@section('content')
<div class="card">

    <div class="card-body">

        <table id="data_table" class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Topic</th>
                    <th scope="col">Log</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                <tr>
                    <td scope="row">{{ $log->id }}</td>
                    <td>{{ $log->topic }}</td>
                    <td>{{ $log->log }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
@endsection

@section('pageJs')
@endsection