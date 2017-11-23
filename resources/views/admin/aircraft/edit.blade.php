@extends('admin.app')
@section('title', "Edit \"$aircraft->name\"")
@section('content')
<div class="card border-blue-bottom">
   <div class="content">
       @include('adminlte-templates::common.errors')
       {!! Form::model($aircraft, ['route' => ['admin.aircraft.update', $aircraft->id], 'method' => 'patch']) !!}

            @include('admin.aircraft.fields')

       {!! Form::close() !!}
   </div>
</div>
@endsection
