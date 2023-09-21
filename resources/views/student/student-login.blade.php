<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title>Student Login</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/themes/adminlte3x/dist/css/adminlte.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/toastr/toastr.min.css">

</head>

<body class="hold-transition layout-top-nav">

    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">

            <div class="container">

                <a href="https://www.ru.ac.bd/" class="navbar-brand">
                    <img src="/logo/ru-logo.png" class="brand-image" alt="Logo">
                </a>

                <button class="navbar-toggler order-1" type="button" data-toggle="collapse"
                    data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Left navbar links -->
                <div class="collapse navbar-collapse order-3" id="navbarCollapse">

                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="btn btn-outline-info" href="{{ route('login') }}" role="button">
                                <i class="fas fa-user-cog"></i>Teacher Login
                            </a>
                        </li>
                        <li class="nav-item ml-lg-2">
                            <a class="btn btn-outline-info" href="https://github.com/sohag1426/RuStudentFeedbackSystem"
                                role="button">
                                <i class="fab fa-github"></i> Source Code
                            </a>
                        </li>
                    </ul>

                </div>
                {{-- Left navbar links --}}

            </div>

        </nav>
        <!-- /navbar -->

        <!-- Content Wrapper-->
        <div class="content-wrapper">

            <!-- Content Header-->
            <div class="content-header">
                <div class="container">
                    <div class="row mb-2">

                    </div>
                    <!--/row -->
                </div>
                <!--/container-fluid -->
            </div>
            <!-- /content-header -->

            <!-- Main content -->
            <div class="content">

                <!-- Modal Loading-->
                <div class="modal fade" id="ModalCenter" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="overlay-wrapper">
                                    <div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>
                                    <div class="text-bold pt-2">Loading...</div>
                                    <div class="text-bold pt-2">Please Wait</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/modal Loading-->

                <div class="container login-box">

                    <div class="card">

                        <!--card-body-->
                        <div class="card-body">

                            <!--Login Message-->
                            <p class="login-box-msg">Student Login</p>
                            <!--/Login Message-->

                            <!--Login form-->
                            <form name="login" action="{{ route('student-login') }}" method="post"
                                onsubmit="return showModal()">

                                @csrf

                                {{-- student_id --}}
                                <div class="input-group mb-3">
                                    <input type="text" name="student_id" class="form-control"
                                        placeholder="Student ID" required>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-user"></span>
                                        </div>
                                    </div>
                                </div>
                                {{-- student_id --}}

                                {{-- password --}}
                                <div class="input-group mb-3">
                                    <input type="password" name="password" class="form-control" placeholder="Password"
                                        autocomplete="new-password" required>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-lock"></span>
                                        </div>
                                    </div>
                                </div>
                                {{-- password --}}

                                <div class="row">
                                    <button type="submit" class="btn btn-block btn-dark btn-sm">SUBMIT</button>
                                </div>

                            </form>
                            <!--/Login form-->

                        </div>
                        <!--/card-body-->

                    </div>
                    <!--/card-->

                </div>
                <!-- /container-fluid -->

            </div>
            <!-- /content -->

        </div>
        <!-- /content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
        </aside>
        <!-- /control-sidebar -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <strong>powered by <a href="https://www.ru.ac.bd/">University of Rajshahi</a></strong>
        </footer>
        <!-- /Main Footer -->
    </div>
    <!--/wrapper -->

    <!-- jQuery -->
    <script src="/themes/adminlte3x/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="/themes/adminlte3x/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="/themes/adminlte3x/dist/js/adminlte.js"></script>
    <!-- SweetAlert2 -->
    <script src="/themes/adminlte3x/plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Toastr -->
    <script src="/themes/adminlte3x/plugins/toastr/toastr.min.js"></script>

    <script type="text/javascript">
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    </script>

    @if (session('success'))
        <script type="text/javascript">
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        </script>
    @endif

    @if (session('info'))
        <script type="text/javascript">
            Toast.fire({
                icon: 'info',
                title: '{{ session('info') }}'
            });
        </script>
    @endif

    @if (session('error'))
        <script type="text/javascript">
            Toast.fire({
                icon: 'error',
                title: '{{ session('error') }}'
            });
        </script>
    @endif

    <script>
        function showModal() {
            $('#ModalCenter').modal({
                backdrop: 'static',
                show: true
            });
            return true;
        }
    </script>

</body>

</html>
