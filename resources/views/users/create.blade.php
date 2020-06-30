@extends("layouts.app")

@section("title") Create New User @endsection

@section('list-admin-create')
{{ Breadcrumbs::render('list-user-create') }}
@endsection

@section("content")

@if(session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Create Users</h3>
    </div>
    <div class="card-body">
        <!-- enctype="multipart/form-data" karena kita akan mengunggah (mengupload) file dari form -->
        <form action="{{ route('users.store') }}" method="POST" id="formKirim" files="true" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <label for="name">Name</label>
                    <input value="{{ old('name') }}"
                        class="form-control {{ $errors->first('name') ? "is-invalid": "" }}"
                        placeholder="Full Name" type="text" name="name" id="name" />
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                    <br>
                    <label for="name">Phone Number</label>
                    <input value="{{ old('phone') }}"
                        class="form-control {{ $errors->first('phone') ? "is-invalid": "" }}"
                        placeholder="Phone Number" type="text" name="phone" id="phone" />
                    <div class="invalid-feedback">
                        {{ $errors->first('phone') }}
                    </div>
                    <br>
                    <label for="password">Password</label>
                    <input
                        class="form-control {{ $errors->first('password') ? "is-invalid" : "" }}"
                        placeholder="password" type="password" name="password" id="password" />
                    <div class="invalid-feedback">
                        {{ $errors->first('password') }}
                    </div>
                    <br>
                    <label for="roles">Role</label>
                    <select name="roles[]" multiple id="roles" class="form-control {{ $errors->first('roles') ? "is-invalid" : "" }}"></select>
                    <div class="invalid-feedback">
                        {{ $errors->first('roles') }}
                    </div>
                    <br>
                </div>
                <div class="col-md-6">
                    <label for="email">Email</label>
                    <input value="{{ old('email') }}"
                        class="form-control {{ $errors->first('email') ? "is-invalid" : "" }}"
                        placeholder="user@mail.com" type="text" name="email" id="email" />
                    <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </div>
                    <br>
                    <label for="username">Username</label>
                    <input value="{{ old('username') }}"
                        class="form-control {{ $errors->first('username') ? "is-invalid" : "" }}"
                        placeholder="Username" type="text" name="username" id="username" />
                    <div class="invalid-feedback">
                        {{ $errors->first('username') }}
                    </div>
                    <br>
                    <label for="password_confirmation">Password Confirmation</label>
                    <input
                        class="form-control {{ $errors->first('password_confirmation') ? "is-invalid" : "" }}"
                        placeholder="password confirmation" type="password" name="password_confirmation"
                        id="password_confirmation" />
                    <div class="invalid-feedback">
                        {{ $errors->first('password_confirmation') }}
                    </div>
                    <br>
                    <label for="gender">Gender</label><br>
                    <input value="Male" type="radio" id="male" name="gender" checked> <label for="male">Male</label>
                    <input value="Female" type="radio" id="female" name="gender"> <label for="female">Female</label>
                    <div class="invalid-feedback">
                        {{ $errors->first('gender') }}
                    </div>
                    <br>
                    <div class="col-sm-12 text-right" style="margin-top:10px;">
                        <input class="btn btn-primary" type="submit" id="button" value="Save" />
                    </div>
                </div> <!-- kanan -->
            </div> <!-- row-->
        </form><!--</form>-->
    </div> <!-- card body -->
    <div class="card-footer">
        Visit <a href="https://select2.github.io/">Select2 documentation</a> for more examples and information about the
        plugin.
    </div>
</div>
@endsection
@section('crud-js') <!--terkait dengan kode@yield('crud-js') di app.blade.php-->
<script>
$('.alert-success').fadeIn().delay(700).fadeOut();
</script>
@endsection
@section('footer-scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
 
<script>
$('#roles').select2({
ajax: {
    url: '{{ url('searchrole') }}',
    processResults: function(data){
    return {
        results: data.map(function(item){return {id: item.id, text: item.name} })
    }
    }
}
});
</script>
@endsection