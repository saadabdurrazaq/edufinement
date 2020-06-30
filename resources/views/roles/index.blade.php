@extends("layouts.app")

@section("title") Users list @endsection 

@section('index-admin-list')
    {{ Breadcrumbs::render('list-roles') }}
@endsection

@section("content")
    
@if ($message = Session::get('success'))
<div class="alert alert-success">
    <p>{{ $message }}</p>
</div>
@endif

<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Role Management</h3>
    </div>
    <div class="card-body">
        <div class="row" style="margin-top:-20px;">
            <div class="col-md-12 text-right">
                @can('Create Roles')
                <a href="{{ route('roles.create') }}" style="margin-top:25px;" class="btn btn-primary">Create Role</a>
                @endcan
            </div>
        </div>
        <hr>
            <div class="panel-body table-responsive" style="overflow:hidden;">
                <table class="table table-bordered table-hover dataTable">
                <thead>
                    <tr>
                        <th><b>No</b></th>
                        <th><b>Name</b></th>
                        <th><b>Action</b></th>
                    </tr>
                </thead>
                <tbody>
        @foreach($roles as $key => $role)
        <tr>
            <td>{{ ++$i }}</td>            
            <td>{{ $role->name }}</td>
            <td>
            <a class="btn btn-info" href="{{ route('roles.show', $role->id) }}">Show</a>
            @can('Edit Roles')
                <a class="btn btn-primary" href="{{ route('roles.edit',$role->id) }}">Edit</a>
            @endcan            
            @can('Delete Roles')
                    {!! Form::open(['method' => 'DELETE','route' => ['roles.destroy', $role->id],'style'=>'display:inline']) !!}
                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                    {!! Form::close() !!}
            @endcan
            </td>
        </tr>
        @endforeach 
        </tbody>
                </table>
            </div> <!-- panel-body table-responsive -->
    </div> <!--card body-->
    <div class="card-footer">
            Visit <a href="https://select2.github.io/">Select2 documentation</a> for more examples and information about
            the plugin.
        </div> 
</div> <!--card card-secondary-->
@endsection
@section('crud-js') <!--terkait dengan kode@yield('crud-js') di app.blade.php-->
<script>
$('.alert-success').fadeIn().delay(500).fadeOut();
</script>
@endsection