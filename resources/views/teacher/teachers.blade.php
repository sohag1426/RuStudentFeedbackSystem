@extends ('laraview.layouts.sideNavLayout')

@section('title')
Teachers
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
<ul class="nav flex-column flex-sm-row ml-4">
    @if (auth('web')->user()->role == 'DepartmentChair')
    <li class="nav-item">
        <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('users.create') }}">
            <i class="fas fa-plus"></i>
            New Teacher
        </a>
    </li>
    @else
    <h1> Teachers </h1>
    @endif
</ul>
@endsection

@section('content')
<div class="card">

    <!--modal -->
    <div class="modal" tabindex="-1" role="dialog" id="modal-default">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="modal-title" class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body overflow-auto" id="ModalBody">
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /modal-content -->
        </div>
        <!-- /modal-dialog -->
    </div>
    <!-- /modal -->

    <div class="card-body">

        <table id="data_table" class="table table-hover">

            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Mobile</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($users as $user)
                <tr>
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