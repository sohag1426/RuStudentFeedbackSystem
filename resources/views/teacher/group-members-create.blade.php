@extends ('laraview.layouts.sideNavLayout')

@section('title')
    Add Students
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '3';
        $active_link = '1';
    @endphp
@endsection

@section('sidebar')
    @include('teacher.sidebar')
@endsection

@section('contentTitle')
    <h3>Add Students</h3>
@endsection

@section('content')
    <div class="card">
        <div class="row">
            <div class="col-sm-6">
                <div class="card-header">

                    {{-- Download --}}
                    <ul class="nav justify-content-start">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('sample-excel') }}">
                                <i class="fas fa-download"></i> Download Sample
                            </a>
                        </li>
                    </ul>
                    {{-- Download --}}

                </div>
            </div>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-sm-6">

                    <form id="quickForm" autocomplete="off" method="POST"
                        action="{{ route('student_groups.student_group_members.store', ['student_group' => $student_group]) }}"
                        enctype="multipart/form-data">

                        @csrf

                        <!--excel_file-->
                        <div class="form-group row">
                            <label for="excel_file">Excel File</label>
                            <div class="custom-file">
                                <input type="file" name="excel_file" class="custom-file-input" id="excel_file" required>
                                <label class="custom-file-label" for="excel_file">Choose file</label>
                            </div>
                        </div>
                        <!--/excel_file-->

                        <button type="submit" class="btn btn-dark">Submit</button>

                    </form>

                </div>
                <!--/col-sm-6-->

            </div>
            <!--/row-->

        </div>
        <!--/card-body-->

    </div>
@endsection

@section('pageJs')
    <script type="text/javascript">
        $(document).ready(function() {
            bsCustomFileInput.init();
        });
    </script>
@endsection
