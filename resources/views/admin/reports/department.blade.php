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
