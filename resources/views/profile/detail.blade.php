<form id="contact_us" method="post" action="{{ route('profile.show', ['id' => $users->id]) }}" class="form-horizontal" enctype="multipart/form-data" class="bg-white shadow-sm p-3">
    @csrf
    <!--Form ini akan dikirimkan ke url http://larashop.test/users/{id} dengan method PUT. -->
    <input type="hidden" value="PUT" name="_method">
    <div class="form-group row">
        <label for="inputName" class="col-sm-2 col-form-label">Name</label>
        <div class="col-sm-10"> 
            <input
                value="{{ old('name') ? old('name') : $users->name }}"
                class="form-control {{ $errors->first('name') ? "is-invalid" : "" }}"
                placeholder="Full Name" type="text" name="name" id="name" /> 
            <div class="invalid-feedback">
                {{ $errors->first('name') }}  
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
        <div class="col-sm-10">
            <input value="{{ $users->email }}" disabled
                class="form-control {{ $errors->first('email') ? "is-invalid" : "" }} "
                placeholder="user@mail.com" type="text" name="email" id="email" />
            <div class="invalid-feedback">
                {{ $errors->first('email') }}
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label for="inputExperience" class="col-sm-2 col-form-label">Username</label>
        <div class="col-sm-10">
            <input value="{{ $users->username }}" disabled class="form-control" placeholder="username"
            type="text" name="username" id="username" />
        </div>
    </div>
    <div class="form-group row">
        <label for="inputExperience" class="col-sm-2 col-form-label">Avatar Image</label>
        <div class="col-sm-10">
            @if($users->avatar == null)
                @if($users->gender == 'Male')
                <img src="{{URL::asset('public/bower_components/admin-lte/dist/img/fb-male.jpg')}}" width="120px" alt="User Image">
                @else
                <img src="{{URL::asset('public/bower_components/admin-lte/dist/img/female-fb.jpg')}}" width="120px" alt="User Image">
                @endif
            @else
               <img src="{{ asset('storage/app/public/'.$users->avatar) }}" width="120px" />
            @endif
            <br>
            <a href="{{ route('delete.avatar', ['id' => $users->id]) }}">Hapus photo</a>
            <br>
            <div class="form-group">
                <div class="file-loading">
                    <input id="avatar" type="file" name="file" multiple class="file" data-iconName="fa fa-upload" data-overwrite-initial="false">
                </div>
            </div>
            <small class="text-muted">Kosongkan jika tidak ingin mengubah avatar</small>
        </div>
    </div>
    <div class="form-group row">
        <div class="offset-sm-2 col-sm-10">
            <button type="submit" id="send_form" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>
<script type="text/javascript">
$(document).on("ready", function() {
    $("#avatar").fileinput({
            theme: 'fa',
            uploadUrl: '{{ url('saveForm') }}',
            uploadExtraData: function() {
                return {
                    _token: $("input[name='_token']").val(),
                };
            },
            allowedFileExtensions: ['jpg', 'png', 'gif'],
            overwriteInitial: false,
            maxFileSize:2000,
            maxFilesNum: 1,
            slugCallback: function (filename) {
                return filename.replace('(', '_').replace(']', '_');
            }
    });
});
</script>