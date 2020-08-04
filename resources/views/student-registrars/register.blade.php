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
                                        <p class="lead section-title">Parents or Guardian Info:</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username">Your Father's Username</label>
                                        <input id="father-username" type="text" class="form-control @error('father-username') is-invalid @enderror" name="father-username" value="{{ old('father-username') }}" required autocomplete="username">
                                        @error('father-username')
                                            <span class="invalid-feedback" role="alert"> 
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="text-muted">Your father/guardian male doesn't have an account yet? Create <a href="{{ route('father-regis') }}">here!</a></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username">Your Father's Account Key</label>
                                        <input id="father-account-key" type="password" class="form-control @error('account-key') is-invalid @enderror" name="account-key" value="{{ old('account-key') }}" required autocomplete="username">
                                        @error('account-key')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <input type="checkbox" id="select-father" name="select-father" value="{{ old('select-father') }}"/><small class="text-muted"> I dont't have a father or my father was dead.</a></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username">Your Mother's Username</label>
                                        <input id="mother-username" type="text" class="form-control @error('mother-username') is-invalid @enderror" name="mother-username" value="{{ old('mother-username') }}" required autocomplete="username">
                                        @error('mother-username')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="text-muted">Your mother/guardian female doesn't have an account yet? Create <a href="{{ route('mother-regis') }}">here!</a></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username">Your Mother's Account Key</label>
                                        <input id="mother-account-key" type="password" class="form-control @error('mother-account-key') is-invalid @enderror" name="mother-account-key" value="{{ old('mother-account-key') }}" required autocomplete="username">
                                        @error('mother-account-key')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <input type="checkbox" id="select-mother" name="select-mother" value="1"/><small class="text-muted"> I dont't have a mother or my mother was dead.</a></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <p class="lead section-title"></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username">Your Guardian's Male Username</label>
                                        <input disabled id="guardianmale-username" type="text" class="form-control @error('guardianmale-username') is-invalid @enderror" name="guardianmale-username" value="{{ old('guardianmale-username') }}" required autocomplete="username">
                                        @error('guardianmale-username')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong> 
                                            </span>
                                        @enderror
                                        <small class="text-muted">Use father/guardian male account you've created at link above</a></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username">Your Guardian's Male Account Key</label>
                                        <input disabled id="guardianmale-account-key" type="password" class="form-control @error('guardianmale-account-key') is-invalid @enderror" name="guardianmale-account-key" value="{{ old('guardianmale-account-key') }}" required autocomplete="username">
                                        @error('guardianmale-account-key')
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
                                        <label for="username">Your Guardian's Female Username</label>
                                        <input disabled id="guardianfemale-username" type="text" class="form-control @error('guardianfemale-username') is-invalid @enderror" name="guardianfemale-username" value="{{ old('guardianfemale-username') }}" required autocomplete="username">
                                        @error('guardianfemale-username')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="text-muted">Use mother/guardian female account you've created at link above</a></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username">Your Guardian's Female Account Key</label>
                                        <input disabled id="guardianfemale-account-key" type="password" class="form-control @error('guardianfemale-account-key') is-invalid @enderror" name="guardianfemale-account-key" value="{{ old('guardianfemale-account-key') }}" required autocomplete="username">
                                        @error('guardianfemale-account-key')
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
@section('crud-js')
<script>
getFather = document.getElementById('select-father');      

getFather.onclick = function() {
    if(document.getElementById('select-father').checked) {
        localStorage.setItem('saveSetting', "1");
        $('#father-username').prop('disabled', true);
        $('#father-account-key').prop('disabled', true);
        $('#guardianmale-username').prop('disabled', false);
        $('#guardianmale-account-key').prop('disabled', false);
        $('#father-username').val('');
        $('#father-account-key').val('');
    } 
    else {
        localStorage.setItem('saveSetting', "0");
        document.getElementById("select-father").checked = false;
        document.getElementById('father-username').disabled = false;
        document.getElementById('father-account-key').disabled = false;
        document.getElementById('guardianmale-username').disabled = true;
        document.getElementById('guardianmale-account-key').disabled = true;
        $('#guardianmale-username').val('');
        $('#guardianmale-account-key').val('');
    }
}

logSaveSetting = localStorage.getItem('saveSetting');
console.log(logSaveSetting);

if (logSaveSetting === "1") {
    document.getElementById("select-father").checked = true;
    document.getElementById('father-username').disabled = true;
    document.getElementById('father-account-key').disabled = true;
    document.getElementById('guardianmale-username').disabled = false;
    document.getElementById('guardianmale-account-key').disabled = false;
} 
else {
    document.getElementById("select-father").checked = false;
    document.getElementById('father-username').disabled = false;
    document.getElementById('father-account-key').disabled = false;
    document.getElementById('guardianmale-username').disabled = true;
    document.getElementById('guardianmale-account-key').disabled = true;
}

///////////////////////////////////////////////////////////////////////

getMother = document.getElementById('select-mother');      

getMother.onclick = function() {
    if(document.getElementById('select-mother').checked) {
        localStorage.setItem('saveMother', "1"); 
        $('#mother-username').prop('disabled', true);
        $('#mother-account-key').prop('disabled', true);
        $('#guardianfemale-username').prop('disabled', false);
        $('#guardianfemale-account-key').prop('disabled', false);
        $('#mother-username').val('');
        $('#mother-account-key').val('');
    } 
    else {
        localStorage.setItem('saveMother', "0");
        document.getElementById("select-mother").checked = false;
        document.getElementById('mother-username').disabled = false;
        document.getElementById('mother-account-key').disabled = false;
        document.getElementById('guardianfemale-username').disabled = true;
        document.getElementById('guardianfemale-account-key').disabled = true;
        $('#guardianfemale-username').val('');
        $('#guardianfemale-account-key').val('');
    }
}

logSaveMother = localStorage.getItem('saveMother');
console.log(logSaveMother);

if (logSaveMother === "1") {
    document.getElementById("select-mother").checked = true;
    document.getElementById('mother-username').disabled = true;
    document.getElementById('mother-account-key').disabled = true;
    document.getElementById('guardianfemale-username').disabled = false;
    document.getElementById('guardianfemale-account-key').disabled = false;
} 
else {
    document.getElementById("select-mother").checked = false;
    document.getElementById('mother-username').disabled = false;
    document.getElementById('mother-account-key').disabled = false;
    document.getElementById('guardianfemale-username').disabled = true;
    document.getElementById('guardianfemale-account-key').disabled = true;
}
///////////////////////////////////////////////////////////////////////////
</script>
@endsection