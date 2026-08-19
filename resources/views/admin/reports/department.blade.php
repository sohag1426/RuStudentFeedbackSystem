@extends('laraview.layouts.sideNavLayout')

@section('title')
Report By Department
@endsection

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('contentTitle')
<h3>Report By Department</h3>
@endsection

@section('content')
@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
<div class="card">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">Department ID</th>
                    <th scope="col">Department Name</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($departments as $department)
                <tr>
                    <td>{{ $department->id }}</td>
                    <td>{{ $department->en_name }}</td>
                    <td>
                        <a href="{{ route('admin-reports.department.download', $department) }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
