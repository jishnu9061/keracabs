<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Dashboard | Greenveel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="" name="description" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('admin/images/logo.png') }}">

    <!-- Include styles -->
    <!-- Bootstrap Css -->
    <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('admin/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('admin/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/css/additional.css') }}" rel="stylesheet" type="text/css" />
    <!-- DataTables -->
    <link href="{{ asset('admin/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('admin/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('admin/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}"
        rel="stylesheet" type="text/css" />
    <!-- choices css -->
    <link href="{{ asset('admin/libs/choices.js/public/assets/styles/choices.min.css') }}" rel="stylesheet"
        type="text/css" />
    <!-- datepicker css -->
    <link rel="stylesheet" href="{{ asset('admin/libs/flatpickr/flatpickr.min.css') }}">

    <link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">


<link rel="stylesheet" href="{{ asset('admin/css/preloader.min.css') }}" type="text/css" />

       <link rel="stylesheet" href="{{ asset('admin/libs/glightbox/css/glightbox.min.css') }}">


</head>

<body>
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('layouts.header')

        <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu">
            <div data-simplebar class="h-100">
                <!--- Sidemenu -->
                @include('layouts.sidebar')
                <!-- Sidebar -->
            </div>
        </div>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            @include('layouts.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>



    <!-- Include scripts -->

    <script src="{{ asset('admin/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('admin/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('admin/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('admin/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('admin/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('admin/libs/pace-js/pace.min.js') }}"></script>
    <script src="{{ asset('admin/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('admin/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
    <script src="{{ asset('admin/libs/admin-resources/jquery.vectormap/maps/jquery-jvectormap-world-mill-en.js') }}">
    </script>
    <script src="{{ asset('admin/js/pages/dashboard.init.js') }}"></script>
    <script src="{{ asset('admin/js/app.js') }}"></script>
    <!-- choices js -->
    <script src="{{ asset('admin/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <!-- color picker js -->
    <script src="{{ asset('admin/libs/@simonwep/pickr/pickr.min.js') }}"></script>
    <script src="{{ asset('admin/libs/@simonwep/pickr/pickr.es5.min.js') }}"></script>
    <!-- datepicker js -->
    <script src="{{ asset('admin/libs/flatpickr/flatpickr.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ asset('admin/js/pages/datatables.init.js') }}"></script>
    <!-- init js -->
    <script src="{{ asset('admin/js/pages/form-advanced.init.js') }}"></script>
    <!-- ckeditor -->
    <script src="{{ asset('admin/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>
    <!-- init js -->
    <script src="{{ asset('admin/js/pages/form-editor.init.js') }}"></script>

    <script src="{{ asset('admin/libs/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('admin/js/pages/lightbox.init.js') }}"></script>


    <!-- Required datatable js -->
    <script src="{{ asset('admin/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('admin/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('admin/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @include('includes.toastr')

</body>

</html>
