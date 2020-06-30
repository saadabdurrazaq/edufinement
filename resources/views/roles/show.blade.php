@extends('layouts.app')

@section('title') Detail user @endsection 

@section('show-admin-list')
{{ Breadcrumbs::render('list-role-detail') }}
@endsection

@section('content')

<div class="col-md-6">
<div class="card card-secondary">
<div class="card-header">
    <h3 class="card-title">Detail</h3>
</div>  
<div class="card-body">
    <b>Name:</b> <br/>
    {{ $role->name }} 
    <br><br>

    <div class="form-group">
        <strong>Permissions:</strong>
        @if(!empty($rolePermissions))
            @foreach($rolePermissions as $v)
                <label class="badge badge-success">{{ $v->name }},</label>
            @endforeach
        @endif
    </div>
</div>
</div>
</div>

@endsection