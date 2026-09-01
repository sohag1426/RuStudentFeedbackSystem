<!DOCTYPE html>
<html>
<head>
    <title>Assessment Events Analytics Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            color: #0056b3;
        }
        .header .meta {
            color: #666;
            font-size: 10px;
        }
        .filters-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 15px;
            font-size: 10px;
        }
        .filters-box strong {
            color: #495057;
        }
        .filters-tag {
            display: inline-block;
            background: #e2e6ea;
            padding: 2px 6px;
            border-radius: 3px;
            margin: 2px 4px 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 5px 6px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #f1f3f5;
            color: #212529;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #fcfcfc;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 20px;
            font-size: 9px;
            color: #888;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Assessment Events Analytics Report</h2>
        <div class="meta">
            Generated on: {{ \Carbon\Carbon::now('Asia/Dhaka')->format('Y-m-d h:i A') }} (Asia/Dhaka) | Total Matching Records: {{ $events->count() }}
        </div>
    </div>

    @if (!empty($filters))
    <div class="filters-box">
        <strong>Applied Filter Criteria:</strong><br>
        @foreach ($filters as $label => $value)
            <span class="filters-tag"><strong>{{ $label }}:</strong> {{ $value }}</span>
        @endforeach
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">#</th>
                <th>Session</th>
                <th>Year</th>
                <th>Semester</th>
                <th>Department</th>
                <th>Teacher</th>
                <th>Course</th>
                <th>Start Time</th>
                <th>Stop Time</th>
                <th style="width: 45px;">Score</th>
                <th style="width: 55px;">%Feedback</th>
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
                <td>{{ $event->score }}</td>
                <td>{{ $event->feedback_percentage }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center" style="padding: 15px; color: #888;">
                    No assessment events found matching the criteria.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        RU Student Feedback System - Assessment Events Analytics
    </div>
</body>
</html>
