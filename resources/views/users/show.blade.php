@extends('layouts.app')

@section('title') Detail user @endsection 

@section('show-admin-list')
{{ Breadcrumbs::render('show', $user->listAdmin, $user) }}
@endsection

@section('content')

<div class="col-md-6">
<div class="card card-secondary">
<div class="card-header">
    <h3 class="card-title">Detail</h3>
</div>  
<div class="card-body">
    <b>Name:</b> <br/>
    {{$user->name}} 
    <br><br>

    <b>Username:</b><br>
    {{$user->email}}

    <br>
    <br>
    <b>Roles:</b> <br>
    @if(!empty($user->getRoleNames()))
        @foreach($user->getRoleNames() as $v)
            <label class="badge badge-success">{{ $v }}</label>
        @endforeach
    @endif
</div>
</div>
</div>

@endsection