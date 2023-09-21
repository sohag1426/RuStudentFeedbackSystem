<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Feedback System | Log in </title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/fontawesome-free/css/all.min.css">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/themes/adminlte3x/dist/css/adminlte.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/toastr/toastr.min.css">
</head>

<body class="hold-transition login-page">

    <div class="login-box">

        <!-- /.login-logo -->

        <div class="card card-outline card-primary">

            <div class="card-header text-center">
                <a href="#" class="h1"><b>Teacher's</b> Login</a>
            </div>

            <div class="card-body">
                <p class="login-box-msg">Sign in to start your session</p>

                <form action="{{ route('login') }}" method="post">
                    @csrf

                    <div class="input-group mb-3">
                        <input id="internet_id " type="number" name="internet_id" class="form-control"
                            placeholder="Internet ID " required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-id-card"></span>
                            </div>
                        </div>
                    </div>

                    @error('internet_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div class="input-group mb-3">
                        <input id="password" type="password" name="password" class="form-control"
                            placeholder="Password" required autocomplete="new-password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>

                    @error('password')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <!-- /col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                        <!-- /col -->
                    </div>

                </form>

            </div>
            <!-- /card-body -->

        </div>
        <!-- /card -->

    </div>
    <!-- /login-box -->

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


</body>

</html>
