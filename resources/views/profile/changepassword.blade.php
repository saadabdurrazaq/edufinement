<form class="form-horizontal" method="POST" action="{{url('password/change')}}">
    {{ csrf_field() }}
    
    <div class="form-group">
        <label for="current_password">Current Password</label>
        <input id="current_password" type="password" class="form-control" name="current_password" required placeholder="Enter Password">
        @if ($errors->has('current_password'))
            <span class="help-block" style="font-size:80%; color:#dc3545;">
                <strong>{{ $errors->first('password') }}</strong>
            </span>
        @endif
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input id="password" type="password" class="form-control {{ $errors->first('password') ? "is-invalid" : "" }}" name="password" required placeholder="Enter at least 6 Character">
        @if ($errors->has('password'))
            <span class="help-block" style="font-size:80%; color:#dc3545;">
                <strong>{{ $errors->first('password') }}</strong>
            </span>
        @endif
    </div>

    <div class="form-group">
        <label for="password-confirm">Confirm Password</label>
        <input id="password-confirm" type="password" class="form-control {{ $errors->first('password_confirmation') ? "is-invalid" : "" }}" name="password_confirmation" required placeholder="Confirm Password">
        @if ($errors->has('password_confirmation'))
            <span class="help-block" style="font-size:80%; color:#dc3545;">
                <strong>{{ $errors->first('password_confirmation') }}</strong>
            </span>
        @endif
    </div>

    <div class="form-group">
        <div class="col-12 text-center">
            <button class="btn btn-primary" type="submit">Submit</button>
        </div>
    </div>
</form>