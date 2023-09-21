@extends ('laraview.layouts.sideNavLayout')

@section('title')
    Questions
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '4';
        $active_link = '1';
    @endphp
@endsection

@section('sidebar')
    @include('teacher.sidebar')
@endsection

@section('contentTitle')
    <h3> Questions for Feedback</h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-body">

            <table id="data_table" class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">English</th>
                        <th scope="col">Bangla</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($questions as $question)
                        <tr>
                            <td scope="row">{{ $question->id }}</td>
                            <td>{{ $question->en }}</td>
                            <td>{{ $question->bn }}</td>
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
