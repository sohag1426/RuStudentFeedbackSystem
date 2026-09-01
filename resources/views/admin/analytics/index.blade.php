@extends('laraview.layouts.sideNavLayout')

@section('title')
Analytics
@endsection

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('contentTitle')
<h3>Analytics</h3>
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

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0"><i class="fas fa-filter mr-2"></i>Filter Assessment Events</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin-analytics.index') }}">
            <div class="form-row">
                {{-- Department --}}
                <div class="form-group col-md-3">
                    <label for="department_id">Department</label>
                    <select name="department_id" id="department_id" class="form-control">
                        <option value="">All Departments</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->en_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Session --}}
                <div class="form-group col-md-3">
                    <label for="session">Session</label>
                    <select name="session" id="session" class="form-control">
                        <option value="">All Sessions</option>
                        @foreach ($sessions as $sessionOption)
                            <option value="{{ $sessionOption }}" {{ request('session') == $sessionOption ? 'selected' : '' }}>
                                {{ $sessionOption }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Year --}}
                <div class="form-group col-md-3">
                    <label for="year">Year</label>
                    <select name="year" id="year" class="form-control">
                        <option value="">All Years</option>
                        @foreach ($years as $yearOption)
                            <option value="{{ $yearOption->value }}" {{ request('year') == $yearOption->value ? 'selected' : '' }}>
                                {{ $yearOption->value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Semester --}}
                <div class="form-group col-md-3">
                    <label for="semester">Semester</label>
                    <select name="semester" id="semester" class="form-control">
                        <option value="">All Semesters</option>
                        @foreach ($semesters as $semesterOption)
                            <option value="{{ $semesterOption->value }}" {{ request('semester') == $semesterOption->value ? 'selected' : '' }}>
                                {{ $semesterOption->value }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                {{-- Start Time (Date) --}}
                <div class="form-group col-md-3">
                    <label for="start_time">Start Date</label>
                    <input type="date" name="start_time" id="start_time" class="form-control" value="{{ request('start_time') }}">
                </div>

                {{-- Stop Time (Date) --}}
                <div class="form-group col-md-3">
                    <label for="stop_time">Stop Date</label>
                    <input type="date" name="stop_time" id="stop_time" class="form-control" value="{{ request('stop_time') }}">
                </div>

                {{-- Score --}}
                <div class="form-group col-md-3">
                    <label for="score">Score</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <select name="score_operator" class="custom-select" style="max-width: 70px;">
                                <option value="=" {{ request('score_operator') == '=' ? 'selected' : '' }}>=</option>
                                <option value=">" {{ request('score_operator') == '>' ? 'selected' : '' }}>&gt;</option>
                                <option value="<" {{ request('score_operator') == '<' ? 'selected' : '' }}>&lt;</option>
                            </select>
                        </div>
                        <input type="number" step="0.01" min="0" max="5" name="score" id="score" class="form-control" placeholder="e.g. 4.00" value="{{ request('score') }}">
                    </div>
                </div>

                {{-- Feedback Percentage --}}
                <div class="form-group col-md-3">
                    <label for="feedback_percentage">%Feedback</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <select name="feedback_percentage_operator" class="custom-select" style="max-width: 70px;">
                                <option value="=" {{ request('feedback_percentage_operator') == '=' ? 'selected' : '' }}>=</option>
                                <option value=">" {{ request('feedback_percentage_operator') == '>' ? 'selected' : '' }}>&gt;</option>
                                <option value="<" {{ request('feedback_percentage_operator') == '<' ? 'selected' : '' }}>&lt;</option>
                            </select>
                        </div>
                        <input type="number" step="0.01" min="0" max="100" name="feedback_percentage" id="feedback_percentage" class="form-control" placeholder="e.g. 75" value="{{ request('feedback_percentage') }}">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('admin-analytics.index') }}" class="btn btn-secondary mr-2">
                    <i class="fas fa-undo mr-1"></i> Reset
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

@if ($hasFilters && $events)
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-table mr-2"></i>Results (Total: {{ $events->total() }})
        </h5>
        @if ($events->total() > 0)
        <a href="{{ route('admin-analytics.download', request()->query()) }}" class="btn btn-success btn-sm">
            <i class="fas fa-file-pdf mr-1"></i> Download PDF
        </a>
        @endif
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Session</th>
                    <th>Year</th>
                    <th>Semester</th>
                    <th>Department</th>
                    <th>Teacher</th>
                    <th>Course</th>
                    <th>Start Time</th>
                    <th>Stop Time</th>
                    <th>Score</th>
                    <th>%Feedback</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($events as $event)
                <tr>
                    <td>{{ $event->id }}</td>
                    <td>{{ $event->session ?? $event->group?->session ?? 'N/A' }}</td>
                    <td>{{ $event->year?->value ?? $event->year ?? $event->group?->year?->value ?? $event->group?->year ?? 'N/A' }}</td>
                    <td>{{ $event->semester?->value ?? $event->semester ?? $event->group?->semester?->value ?? $event->group?->semester ?? 'N/A' }}</td>
                    <td>{{ $event->department->en_name ?? 'N/A' }}</td>
                    <td>{{ $event->teacher->name ?? 'N/A' }}</td>
                    <td>{{ $event->course->name ?? 'N/A' }} ({{ $event->course->code ?? '' }})</td>
                    <td>{{ $event->start_time }}</td>
                    <td>{{ $event->stop_time }}</td>
                    <td>
                        @if ($event->score !== 'undefined')
                            <span class="badge badge-info">{{ $event->score }}</span>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td>{{ $event->feedback_percentage }}%</td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center py-4 text-muted">
                        No assessment events found matching the criteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($events->hasPages())
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                Showing {{ $events->firstItem() }} to {{ $events->lastItem() }} of {{ $events->total() }} entries
            </div>
            <div>
                {{ $events->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
@endif
@endsection
