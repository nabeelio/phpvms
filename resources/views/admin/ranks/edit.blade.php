@extends('admin.app')
@section('title', "Edit \"$rank->name\"")
@section('content')
<div class="card border-blue-bottom">
   <div class="content">
       {!! Form::model($rank, ['route' => ['admin.ranks.update', $rank->id], 'method' => 'patch']) !!}
            @include('admin.ranks.fields')
       {!! Form::close() !!}
   </div>
</div>

<div class="card border-blue-bottom">
    <div class="header">
       <h3>subfleets</h3>
    </div>
    <div class="content">
       <div class="row">
           @include('admin.ranks.subfleets')
       </div>
    </div>
</div>
@endsection
@include('admin.ranks.scripts')
