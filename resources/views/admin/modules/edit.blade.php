@extends('admin.app')
@section('title', "Edit " . $module->name)
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      <h5>Change Module Status : </h5>
      {{Form::open(['route' => ['admin.modules.update', $module->id]])}}

      <div class="form-group">
        {{Form::label('Enabled ?')}}
        {{Form::checkbox('enabled', '', $module->enabled)}}
      </div>

      {{ Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) }}
      {{Form::close()}}

      <hr>

      <h5>Delete Module</h5>
      <div class="row">
        <div class="col-lg-6">
          {{Form::open(['route' => ['admin.modules.destroy', $module->id], 'method' => 'delete'])}}

          <div class="form-group">
            Type in <b>{{strtoupper($module->name)}}</b> to Delete :
            {{Form::text('verify', '', ['class' => 'form-control', 'required' => 'required'])}}
          </div>

          {{ Form::button('Delete', ['type' => 'submit', 'class' => 'btn btn-danger']) }}
          {{Form::close()}}
        </div>
      </div>
    </div>
  </div>
@endsection
