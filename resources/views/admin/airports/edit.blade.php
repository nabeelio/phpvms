@extends('admin.app')
@section('title', "Edit \"$airport->name\"")
@section('content')
<div class="card border-blue-bottom">
   <div class="content">
       @include('adminlte-templates::common.errors')
       {!! Form::model($airport, ['route' => ['admin.airports.update', $airport->id], 'method' => 'patch']) !!}
            @include('admin.airports.fields')
       {!! Form::close() !!}
   </div>
</div>
@endsection
