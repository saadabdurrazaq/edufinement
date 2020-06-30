@extends("layouts.app")

@section("title") Create Roles @endsection 

@section('index-admin-list')
    {{ Breadcrumbs::render('list-role-create') }}
@endsection

@section("content")
    
@if ($message = Session::get('success'))
<div class="alert alert-success">
    <p>{{ $message }}</p>
</div>
@endif

@if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>Whoops!</strong> There were some problems with your input.<br><br>
        <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
@endif

<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Create Role</h3>
    </div>
    <div class="card-body">
        {!! Form::open(array('route' => 'roles.store','method'=>'POST')) !!}
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Name:</strong>
                    {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
                </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 panel-body table-responsive" style="overflow:hidden;">
                <table class="table table-hover table-striped dataTable" style="border:1px solid rgba(0,0,0,.125);">
                    <tbody>
                        <strong>Roles Management</strong>
                        @foreach($permission->take(4) as $key => $value)
                        <tr>
                            <td>
                                <input type="checkbox" name="permission[]" data-bootstrap-switch data-off-color="danger" value="{{ $value->id }}"> {{ $value->name }} <br>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <br>
                <table class="table table-hover table-striped dataTable" style="border:1px solid rgba(0,0,0,.125);">
                    <tbody>
                        <strong>Applicant Management</strong>
                        @foreach($permission->slice(13) as $key => $value)
                        <tr>
                            <td>
                                <input type="checkbox" name="permission[]" data-bootstrap-switch data-off-color="danger" value="{{ $value->id }}"> {{ $value->name }} <br>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 panel-body table-responsive" style="overflow:hidden;">
                <table class="table table-hover table-striped dataTable" style="border:1px solid rgba(0,0,0,.125);">
                    <tbody>
                        <strong>Users Management</strong>
                        @foreach($permission->slice(4, 9) as $key => $value)
                        <tr>
                            <td>
                                <input type="checkbox" name="permission[]" data-bootstrap-switch data-off-color="danger" value="{{ $value->id }}"> {{ $value->name }} <br>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-right" style="margin-top:10px;">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div> <!--card body-->
    <div class="card-footer">
            Visit <a href="https://select2.github.io/">Select2 documentation</a> for more examples and information about
            the plugin.
        </div> 
</div> <!--card card-secondary-->
@endsection
@section('crud-js') <!--terkait dengan kode@yield('crud-js') di app.blade.php-->
<script>
    $("input[data-bootstrap-switch]").each(function () {
        $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });
</script>
@endsection