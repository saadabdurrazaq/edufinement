<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"> 
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
 
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset("public/bower_components/admin-lte/dist/css/adminlte.min.css")}}">
    <script src="{{ asset ("public/bower_components/admin-lte/plugins/fontawesome-free/css/all.min.css") }}"></script> 
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.0/css/select.dataTables.min.css"/>
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.standalone.min.css"/>
</head>
<body class="login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ URL::to('login') }}">User Login</a>
        </div>
        <!-- /.login-logo -->
        @include('layouts.errors-and-messages')
        <!-- flash message for succesful verification-->
        @if (Session::has('success'))
            <div class="alert alert-success">
            {!!Session::get('success')!!}.<br><br>
            </div>
        @endif
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning">
                {{ session('warning') }}
            </div>
        @endif
        <div class="card">
            <div class="card-body login-card-body">
            <p class="login-box-msg">Sign in to start your session</p>

            <form method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="email" placeholder="Email Address" required="" autofocus="" /><br>
                    <div class="input-group-append">
                        <div class="input-group-text">
                        <span class="fa fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Password" required=""/><br>
                    <div class="input-group-append">
                        <div class="input-group-text">
                        <span class="fa fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> 
                        <label for="remember">
                            Remember Me
                        </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            <div class="social-auth-links text-center mb-3">
                <p>- OR -</p>
                <a href="{{ url('/redirect') }}" class="btn btn-block btn-primary"><i class="fa fa-facebook mr-2"></i> Sign in using
                    Facebook</a>
                <a href="{{ route('google') }}" class="btn btn-block btn-danger"><i class="fa fa-google-plus mr-2"></i> Sign in using
                    Google+</a>
            </div>
            <!-- /.social-auth-links --> 
 
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">
                    {{ __('Forgot Your Password?') }}
                </a>
            @endif <br>
            <a href="{{ route('register') }}" class="text-center">Register a new membership</a>

            </div>
        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->
    <script src="{{ asset('js/admin.min.js') }}"></script>
</body>
</html>