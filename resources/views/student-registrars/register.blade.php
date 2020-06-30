@extends('layouts.global')

@section('content')
<div class="container">
    <div class="row justify-content-center"> 
        <div class="col-md-8">
            @include('layouts.errors-and-messages')
            @if (Session::has('success'))
                <div class="alert alert-success">
                {!!Session::get('success')!!}.<br><br>
                </div>
            @endif
            @if (session('warning'))
                <div class="alert alert-warning">
                    {{ session('warning') }}
                </div> 
            @endif
            @if(session('status'))
            <div class="alert alert-success">
            {{session('status')}}
            </div>
            @endif 
            <div class="card">
                <div class="card-header">Register for Students</div>

                <div class="card-body">
                    <form class="form-horizontal" method="POST" action="{{ route('student-registrars.store') }}">
                        @csrf
                        
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <p class="lead section-title">Credential Info:</p>
                                    </div>
                                </div>
                            </div>
                            <!-- row 1 -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">{{ __('Name') }}</label>
                                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
    
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email">{{ __('E-Mail Address') }}</label>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="phone">Phone & WhatsApp Number</label>
                                        <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required autocomplete="phone">
                                        @error('phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- End row 1 -->

                            <!-- row 2 -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username">
                                        @error('username')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="password">{{ __('Password') }}</label>
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="password-confirm">{{ __('Confirm Password') }}</label>
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>
                            <!-- End row 2 -->

                            <!-- row 3 -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label for="gender">Gender</label>
                                            <br>
                                            <input value="Male" type="radio" id="male" name="gender" checked> <label for="male">Male</label>
                                            <input value="Female" type="radio" id="female" name="gender"> <label for="female">Female</label>
                                            @error('gender')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End row 3 -->
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <p class="lead section-title">Parents Info:</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username">Your Father's Username</label>
                                        <input id="username" type="text" class="form-control @error('father-username') is-invalid @enderror" name="father-username" value="{{ old('father-username') }}" required autocomplete="username">
                                        @error('father-username')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="text-muted">Your father doesn't have an account yet? Create <a href="{{ route('father-regis') }}">here!</a></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username">Your Father's Account Key</label>
                                        <input id="username" type="password" class="form-control @error('account-key') is-invalid @enderror" name="account-key" value="{{ old('account-key') }}" required autocomplete="username">
                                        @error('account-key')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username">Your Mother's Username</label>
                                        <input id="username" type="text" class="form-control @error('mother-username') is-invalid @enderror" name="mother-username" value="{{ old('mother-username') }}" required autocomplete="username">
                                        @error('mother-username')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="text-muted">Your mother doesn't have an account yet? Create <a href="{{ route('mother-regis') }}">here!</a></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username">Your Mother's Account Key</label>
                                        <input id="username" type="password" class="form-control @error('mother-account-key') is-invalid @enderror" name="mother-account-key" value="{{ old('mother-account-key') }}" required autocomplete="username">
                                        @error('mother-account-key')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="box-footer text-right">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection
