@extends ('laraview.layouts.sideNavLayout')

@section('title')
Officer/Staff
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
<ul class="nav flex-column flex-sm-row ml-4">
    @if (auth('web')->user()->role == 'DepartmentChair')
    <li class="nav-item">
        <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('department-manager.create') }}">
            <i class="fas fa-plus"></i>
            New Officer/Staff
        </a>
    </li>
    @else
    <h1> Officers/Staffs </h1>
    @endif
</ul>
@endsection

@section('content')
<div class="card">

    <div class="card-body">

        <table id="data_table" class="table table-hover">

            <thead>
                <tr>
                    <th scope="col">Internet ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Mobile</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td>{{ $user->internet_id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->mobile }}</td>
                </tr>
                @endforeach
            </tbody>

        </table>

    </div>
    <!--/card body-->

</div>
@endsection

@section('pageJs')
@endsection