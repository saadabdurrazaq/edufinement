@extends('layouts.app')

@section('title') Detail user @endsection 

@section('show-admin-list')
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
</div>
</div>
</div>

@endsection