<!DOCTYPE html>
<html>
<head>
    <title>Department Report - {{ $department->en_name }}</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; font-size: 11px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Feedback Scores Report by Department</h2>
    <p><strong>Department:</strong> {{ $department->en_name }}</p>

    <table>
        <thead>
            <tr>
                <th>Event ID</th>
                <th>Session</th>
                <th>Year</th>
                <th>Semester</th>
                <th>Teacher</th>
                <th>Course</th>
                <th>Start Time</th>
                <th>Score</th>
                <th>%Feedback</th>
                <th>Group Average</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
            <tr>
                <td>{{ $event->id }}</td>
                <td>{{ $event->session ?? $event->group?->session ?? 'N/A' }}</td>
                <td>{{ $event->year?->value ?? $event->year ?? $event->group?->year?->value ?? $event->group?->year ?? 'N/A' }}</td>
                <td>{{ $event->semester?->value ?? $event->semester ?? $event->group?->semester?->value ?? $event->group?->semester ?? 'N/A' }}</td>
                <td>{{ $event->teacher->name ?? 'N/A' }}</td>
                <td>{{ $event->course->name ?? 'N/A' }} ({{ $event->course->code ?? '' }})</td>
                <td>{{ $event->start_time }}</td>
                <td>{{ $event->score }}</td>
                <td>{{ $event->feedback_percentage }}%</td>
                <td>{{ $event->group_average }}</td>
            </tr>
            @endforeach
            @if($events->isEmpty())
            <tr>
                <td colspan="10" style="text-align: center;">No assessment events found.</td>
            </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
