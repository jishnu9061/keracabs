<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Login | Greenveel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="" name="description" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('admin/images/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=McLaren&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    <!-- preloader css -->
    <link rel="stylesheet" href="{{ asset('admin/css/preloader.min.css') }}" type="text/css" />

    <!-- Bootstrap Css -->
    <link rel="stylesheet" href="{{ asset('admin/css/bootstrap.min.css') }}" type="text/css" />

    <!-- Icons Css -->
    <link rel="stylesheet" href="{{ asset('admin/css/icons.min.css') }}" type="text/css" />

    <!-- App Css-->
    <link rel="stylesheet" href="{{ asset('admin/css/app.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('admin/css/additional.css') }}" type="text/css" />

    <style>
        .auth-content .invalid-feedback {
            display: block;
        }
    </style>
</head>

<body>
    <div class="auth-page">
        <div class="container-fluid p-0">
            <div class="row g-0">
                <div class="col-xxl-12 col-lg-12 col-md-12">
                    <div class="auth-bg pt-md-5 p-4 d-flex justify-content-center align-items-center">
                        <div class="bg-overlay bg-primary"></div>
                        <ul class="bg-bubbles">
                            <!-- Background bubbles animation -->
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                        </ul>
                        <div class="row justify-content-center align-items-center">
                            <div class="col-xl-12">
                                <div class="p-0 p-sm-4 px-xl-0">
                                    <div class="auth-full-page-content d-flex p-sm-5 p-4 ">
                                        <div class="w-100">
                                            <div class="d-flex flex-column h-100">
                                                <div class="mb-4 mb-md-5 text-center">
                                                    <a href="index.html" class="d-block auth-logo">
                                                        <img src="{{ asset('admin/images/logo.png') }}" alt=""
                                                            height="58">
                                                    </a>
                                                </div>
                                                <div class="auth-content my-auto">
                                                    <div class="text-center">
                                                        <h5 class="mb-0">Welcome Back !</h5>
                                                        <p class="text-muted mt-2">Sign in to continue to Greenveel.</p>
                                                    </div>
                                                    <form class="mt-4 pt-2" action="{{ route('admin.do-login') }}"
                                                        method="POST">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label class="form-label">Username</label>
                                                            <input type="text"
                                                                class="form-control @error('email') is-invalid @enderror"
                                                                id="email" name="email" placeholder="Enter email">
                                                            @error('email')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Password</label>
                                                            <div class="input-group auth-pass-inputgroup">
                                                                <input type="password"
                                                                    class="form-control @error('password') is-invalid @enderror"
                                                                    id="password" name="password"
                                                                    placeholder="Enter password" aria-label="Password"
                                                                    aria-describedby="password-addon">
                                                                <button class="btn btn-light shadow-none ms-0"
                                                                    type="button" id="password-addon"><i
                                                                        class="mdi mdi-eye-outline"></i></button>
                                                            </div>
                                                            @error('password')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        @error('invalid')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <br>
                                                        <div class="mb-3">
                                                            <a href="{{ route('password.request') }}" class="text-muted">Forgot
                                                                password?</a>
                                                        </div>
                                                        <div class="mb-3">
                                                            <button
                                                                class="btn btn-primary w-100 waves-effect waves-light"
                                                                type="submit">Log In</button>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="mt-4 mt-md-5 text-center">
                                                    <p class="mb-0">©
                                                        <script>
                                                            document.write(new Date().getFullYear())
                                                        </script> Greenveel . Crafted with <i
                                                            class="mdi mdi-heart text-danger"></i> by Codeneos
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('admin/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('admin/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('admin/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('admin/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('admin/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('admin/libs/pace-js/pace.min.js') }}"></script>
    <script src="{{ asset('admin/js/pages/pass-addon.init.js') }}"></script>
</body>

</html>
