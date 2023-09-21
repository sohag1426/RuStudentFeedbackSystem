@extends ('laraview.layouts.sideNavLayout')

@section('title')
    courses
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '7';
        $active_link = '1';
    @endphp
@endsection

@section('sidebar')
    @include('teacher.sidebar')
@endsection

@section('contentTitle')
    <h3> Change Logs </h3>
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
                        <th scope="col">User</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td scope="row">{{ $log->id }}</td>
                            <td>{{ $log->topic }}</td>
                            <td>{{ $log->log }}</td>
                            <td>{{ $log->user->name }}</td>
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
