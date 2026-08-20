@extends('laraview.layouts.sideNavLayout')

@section('title')
Report By Teacher
@endsection

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('contentTitle')
<h3>Report By Teacher</h3>
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
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin-reports.by-teacher') }}" class="form-inline">
            <div class="form-group mr-2">
                <label for="department_id" class="mr-2">Department:</label>
                <select name="department_id" id="department_id" class="form-control" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->en_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <noscript><button type="submit" class="btn btn-primary">Filter</button></noscript>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">Teacher ID</th>
                    <th scope="col">Teacher Name</th>
                    <th scope="col">Department</th>
                    <th scope="col">Email</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($teachers as $teacher)
                <tr>
                    <td>{{ $teacher->id }}</td>
                    <td>{{ $teacher->name }}</td>
                    <td>
                        @php $teacherDept = $departments->firstWhere('id', $teacher->department_id); @endphp
                        {{ $teacherDept ? $teacherDept->en_name : 'N/A' }}
                    </td>
                    <td>{{ $teacher->email }}</td>
                    <td>
                        @if ($teacher->assessment_events_count > 0)
                        <a href="{{ route('admin-reports.teacher.download', $teacher) }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </a>
                        @else
                        <span class="text-danger font-weight-bold">0 Feedback</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-sm-2">
                Total Entries: {{ $teachers->total() }}
            </div>
            <div class="col-sm-10">
                {{ $teachers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
