<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="csrf-token" content="{{ csrf_token() }}" />
<title>AdminLTE 3 | Starter</title>
        <link rel="stylesheet" href="{{ asset("public/css/select.css")}}">
        <link rel="stylesheet" href="{{ asset("public/css/custom.css")}}">
        <link href="{{asset('vendor/harimayco/laravel-menu/assets/style.css')}}" rel="stylesheet">
    <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="{{ asset("public/bower_components/admin-lte/plugins/fontawesome-free/css/all.min.css")}}"> 
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ asset("public/bower_components/admin-lte/dist/css/adminlte.min.css")}}">
        <!-- Google Font: Source Sans Pro -->
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css">
        <link rel="stylesheet" href="//cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css"/>
        <link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.0/css/select.dataTables.min.css"/>
        <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.standalone.min.css"/>
<!-- dropzone css dan js wajib diletakkan di antara tag head-->
<link rel="stylesheet" href="https://toert.github.io/Isolated-Bootstrap/versions/3.3.7/iso_bootstrap3.3.7min.css">  
<!-- file input -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.7/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" media="all" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" crossorigin="anonymous">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
<!-- Top Navbar -->
@include('layouts.adminlte.header')
@yield('head')
<!-- /.navbar -->

<!-- Main Sidebar Container Left -->
@include('layouts.adminlte.sidebar')    

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (breadcrumbs) (Page header) -->
    <div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
        <div class="col-sm-6">
            @yield('title2')
        </div><!-- /.col -->
        <div class="col-sm-6">
            <!-- breadcrumbs -->
            <ol class="breadcrumb float-sm-right">
            @yield('home')
            @yield('index-admin-list')
            @yield('show-admin-list-trash')
            @yield('show-admin-list')
            @yield('list-admin-create')
            @yield('edit-admin-list')
            @yield('active-admin-list')
            @yield('profile')
            </ol>
        </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
    @yield('content')
    @yield('loader')
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
    

<!-- Control Sidebar Right Hide Show -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
    <h5>Titlee</h5>
    <p>Sidebar content</p>
    </div>
</aside>
<!-- /.control-sidebar -->

<!-- Main Footer -->
@include('layouts.adminlte.footer')
@yield('footer') 
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->

<!-- Bootstrap 4 -->  
<!-- jQuery -->
<script src="{{ asset ("public/bower_components/admin-lte/plugins/jquery/jquery.min.js") }}"></script> 
<script src="{{ asset ("public/bower_components/admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js") }}"></script>
    <!-- AdminLTE App -->
<script src="{{ asset ("public/bower_components/admin-lte/dist/js/adminlte.min.js") }}"></script>
<script src="{{ asset ("public/bower_components/admin-lte/plugins/bootstrap-switch/js/bootstrap-switch.min.js") }}"></script>
<!-- file input -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.7/js/fileinput.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.7/themes/fa/theme.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" type="text/javascript"></script>
@yield('crud-js') <!-- http://localhost/my-project/academic/usersadmin -->
@yield('trash')
@yield('footer-scripts')
@stack('custom-scripts')
</body>
</html>
