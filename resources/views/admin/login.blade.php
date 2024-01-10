<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Log in</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/fontawesome-free/css/all.min.css">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/sweetalert2/sweetalert2.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/toastr/toastr.min.css">
    {{-- jquery-ui --}}
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/jquery-ui/jquery-ui.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/themes/adminlte3x/dist/css/adminlte.min.css">

</head>

<body class="hold-transition login-page">

    <div class="login-box">

        <div class="card card-danger">

            <div class="card-header text-center">
                ADMIN LOGIN
            </div>
            <!-- /card-header -->

            <div class="card-body login-card-body">

                <form method="POST" action="{{ route('admin-login') }}" onsubmit="return disableDuplicateSubmit()">

                    @csrf

                    <!--email-->
                    <div class="input-group mb-3">
                        <input name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            placeholder="Email" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                    </div>
                    <!--/email-->

                    <!--password-->
                    <div class="input-group mb-3">
                        <input name="password" type="password"
                            class="form-control @error('password') is-invalid @enderror" placeholder="password" required
                            autocomplete="current-password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                    </div>
                    <!--/password-->

                    <div class="row">
                        <!--Remember Me-->
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember" name="remember"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <!--/Remember Me-->

                        <!--submit-->
                        <div class="col-4">
                            <button type="submit" id="submit-button" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                        <!--/submit-->
                    </div>

                </form>

            </div>
            <!-- /login-card-body -->

        </div>

    </div>
    <!-- /login-box -->

    <!-- jQuery -->
    <script src="/themes/adminlte3x/plugins/jquery/jquery.min.js"></script>
    <!--jquery-ui-1.12.1-->
    <script src="/themes/adminlte3x/plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="/themes/adminlte3x/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="/themes/adminlte3x/plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Toastr -->
    <script src="/themes/adminlte3x/plugins/toastr/toastr.min.js"></script>
    <!-- AdminLTE App -->
    <script src="/themes/adminlte3x/dist/js/adminlte.min.js"></script>

    <script type="text/javascript">
        function disableDuplicateSubmit() {
            let selector = "#submit-button";
            $(selector).prop('disabled', true);
            $(selector).html('<i class="fas fa-sync-alt fa-spin"></i>');
            return true;
        }
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 6000
        });
    </script>

    @if (session('success'))
        <script type="text/javascript">
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            })
        </script>
    @endif

    @if (session('error'))
        <script type="text/javascript">
            Toast.fire({
                icon: 'error',
                title: '{{ session('error') }}'
            })
        </script>
    @endif

    @if (session('info'))
        <script type="text/javascript">
            Toast.fire({
                icon: 'info',
                title: '{{ session('info') }}'
            })
        </script>
    @endif

    @if (session('warning'))
        <script type="text/javascript">
            Toast.fire({
                icon: 'warning',
                title: '{{ session('warning') }}'
            })
        </script>
    @endif

</body>

</html>
