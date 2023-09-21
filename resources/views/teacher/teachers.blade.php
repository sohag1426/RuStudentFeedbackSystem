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
    <h3> Teachers </h3>
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
                    </tr>
                </thead>

                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
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
