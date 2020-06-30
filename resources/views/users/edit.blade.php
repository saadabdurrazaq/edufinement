@extends('layouts.app')

@section('title') Edit User @endsection

@section('edit-admin-list') 
{{ Breadcrumbs::render('edit', $user->listAdmin, $user) }}
@endsection

@section('content')

@if ($message = Session::get('success'))
<div class="alert alert-success">
    <p>{{ $message }}</p>
</div>
@endif

<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Update User</h3>
    </div>
    <div class="card-body">
        <!-- Untuk menggunakan method selain GET dan POST, maka kita harus memberikan nilai method pada form sebagai POST -->
        {!! Form::model($user, ['method' => 'PATCH', 'route' => ['users.update', $user->id]]) !!}
        <div class="row">
            <div class="col-md-6">
                <label for="name">Name</label>
                <input
                    value="{{ old('name') ? old('name') : $user->name }}"
                    class="form-control {{ $errors->first('name') ? "is-invalid" : "" }}"
                    placeholder="Full Name" type="text" name="name" id="name" />
                <div class="invalid-feedback">
                    {{ $errors->first('name') }}
                </div>
                <br>
                <label for="phone">Phone Number</label>
                <input
                value="{{ old('phone') ? old('phone') : $user->phone }}"
                class="form-control {{ $errors->first('phone') ? "is-invalid" : "" }}"
                placeholder="Phone Number" type="text" name="phone" id="phone" />
                <div class="invalid-feedback">
                    {{ $errors->first('phone') }}
                </div>
                <br>
                <label for="gender">Gender</label><br>
                <input value="Male" @if($user['gender'] == 'Male') checked @endif type="radio" id="male" name="gender" checked> <label for="male">Male</label>
                <input value="Female" @if($user['gender'] == 'Female') checked @endif type="radio" id="female" name="gender"> <label for="female">Female</label>
                <div class="invalid-feedback">
                    {{ $errors->first('gender') }}
                </div>
            </div>
            <div class="col-md-6">
                <label for="email">Email</label>
                <input
                value="{{ old('email') ? old('email') : $user->email }}"
                class="form-control {{ $errors->first('email') ? "is-invalid" : "" }}"
                placeholder="Phone Number" type="text" name="email" id="email" />
                <div class="invalid-feedback">
                    {{ $errors->first('email') }}
                </div>
                <br>
                <label for="email">Role</label>
                <select selected="selected" multiple class="form-control {{ $errors->first('roles') ? "is-invalid" : "" }}" name="roles[]" id="roles"></select>
                <div class="invalid-feedback">
                    {{ $errors->first('roles') }}
                </div>
                <br>
            </div>
            <div class="col-sm-12 text-right">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
<div class="card-footer">
    Visit <a href="https://select2.github.io/">Select2 documentation</a> for more examples and information about the
    plugin.
</div>
</div>
@endsection
@section('crud-js') <!--terkait dengan kode@yield('crud-js') di app.blade.php-->
<script>
$('.alert-success').fadeIn().delay(500).fadeOut();
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

var roles = {!! $user->roles !!}

roles.forEach(function(role){
    var option = new Option(role.name, role.id, true, true);
    $('#roles').append(option).trigger('change');
});

</script>
@endsection