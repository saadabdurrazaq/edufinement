@extends('layouts.app')

@section('title') Detail user @endsection 

@section('content')

<div class="col-md-6">
<div class="card card-secondary">
<div class="card-header">
    <h3 class="card-title">Detail</h3>
</div>  
<div class="card-body">
    <b>Name:</b> <br/>
    {{$mother->name}}  
    <br><br>

    <b>Username:</b><br>
    {{$mother->email}}

    <br>
    <br>
    <b>Roles:</b> <br>
    @if(!empty($mother->getRoleNames()))
        @foreach($mother->getRoleNames() as $v)
            <label class="badge badge-success">{{ $v }}</label>
        @endforeach
    @endif
</div>
</div>
</div>

@endsection