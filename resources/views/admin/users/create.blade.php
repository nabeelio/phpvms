@extends('admin.app')
@section('title', 'Add User')
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::open(['route' => 'admin.users.store', 'autocomplete' => false]) }}
      @include('admin.users.fields')

      <div class="row">
        <div class="form-group col-sm-12 text-right">
          {{ Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) }}
          <a href="{{ route('admin.users.index') }}" class="btn btn-default">Cancel</a>
        </div>
      </div>
      {{ Form::close() }}
    </div>
  </div>
@endsection

@include('admin.users.script')
