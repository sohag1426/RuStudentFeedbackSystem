@extends ('laraview.layouts.sideNavLayout')

@section('title')
    Feedback Events
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '5';
        $active_link = '1';
    @endphp
@endsection

@section('sidebar')
    @include('teacher.sidebar')
@endsection

@section('contentTitle')
    <ul class="nav flex-column flex-sm-row ml-4">
        <!--New New Feedback Event-->
        <li class="nav-item">
            <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('assessment_events.create') }}">
                <i class="fas fa-plus"></i>
                New Feedback Event
            </a>
        </li>
        <!--/New New Feedback Event-->
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
                        <th scope="col">#</th>
                        <th scope="col">Teacher</th>
                        <th scope="col">Course</th>
                        <th scope="col">Student Group</th>
                        <th scope="col">Start Time</th>
                        <th scope="col">Stop Time</th>
                        <th scope="col">Created By</th>
                        <th scope="col">Score</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assessment_events as $assessment_event)
                        <tr>
                            <td scope="row">{{ $assessment_event->id }}</td>
                            <td>{{ $assessment_event->teacher->name }}</td>
                            <td>
                                {{ $assessment_event->course->name }} ({{ $assessment_event->course->code }})
                            </td>
                            <td>{{ $assessment_event->group->name }}</td>
                            <td>{{ $assessment_event->start_time }}</td>
                            <td>{{ $assessment_event->stop_time }}</td>
                            <td>{{ $assessment_event->user->name }}</td>

                            @if (auth()->user()->can('viewScore', $assessment_event))
                                <td>{{ $assessment_event->score }}</td>
                            @else
                                <td>***</td>
                            @endif

                            <td>

                                <a class="btn btn-outline-info btn-sm mb-2"
                                    href="{{ route('assessment_events.assessment_event_students.index', ['assessment_event' => $assessment_event]) }}">
                                    <i class="fas fa-info-circle"></i>
                                    Students
                                </a>

                                <a class="btn btn-outline-info btn-sm mb-2"
                                    href="{{ route('assessment_events.status.index', ['assessment_event' => $assessment_event]) }}">
                                    <i class="fas fa-info-circle"></i>
                                    Feedback Status
                                </a>

                                {{-- Generate Report --}}
                                @can('generateReport', $assessment_event)
                                    <a class="btn btn-outline-info btn-sm mb-2"
                                        href="{{ route('generate-score', ['assessment_event' => $assessment_event]) }}">
                                        <i class="fas fa-user-edit"></i>
                                        Generate Report
                                    </a>
                                @endcan
                                {{-- Generate Report --}}

                                {{-- Download Report --}}
                                @can('downloadReport', $assessment_event)
                                    <a class="btn btn-outline-info btn-sm mb-2"
                                        href="{{ route('download-score', ['assessment_event' => $assessment_event]) }}">
                                        <i class="fas fa-download"></i>
                                        Download Report
                                    </a>
                                @endcan
                                {{-- Download Report --}}

                                {{-- EDIT & DELETE --}}
                                @can('delete', $assessment_event)
                                    <a class="btn btn-outline-info btn-sm mb-2"
                                        href="{{ route('assessment_events.edit', ['assessment_event' => $assessment_event]) }}">
                                        <i class="fas fa-edit"></i>
                                        EDIT
                                    </a>
                                    <form method="POST"
                                        action="{{ route('assessment_events.destroy', ['assessment_event' => $assessment_event]) }}"
                                        onsubmit="return confirm('Are you sure you want to remove the item?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">DELETE</button>
                                    </form>
                                @endcan
                                {{-- EDIT & DELETE --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
        <!--/card body-->

    </div>
@endsection

@section('pageJs')
    <script>
        function showGroupMembers(url) {
            $("#modal-title").html("Students");
            $("#ModalBody").html('<div class="overlay"><i class="fas fa-sync-alt fa-spin"></i></div>');
            $("#ModalBody").append('<div class="text-bold pt-2">Loading...</div>');
            $("#ModalBody").append('<div class="text-bold pt-2">Please Wait</div>');
            $('#modal-default').modal('show');
            $.get(url, function(data) {
                $("#ModalBody").html(data);
            });
        }
    </script>
@endsection
