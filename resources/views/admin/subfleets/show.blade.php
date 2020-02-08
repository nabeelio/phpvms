@extends('admin.app')

@section('title', "$subfleet->name")
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.subfleets.show_fields')
    </div>
  </div>

  <div class="card border-blue-bottom">
    <div class="content">
      <h3>fares</h3>
      @component('admin.components.info')
        Fares assigned to the current subfleet. These can be overridden,
        otherwise, the value used is the default, which comes from the fare.
      @endcomponent

      @include('admin.subfleets.fares')
    </div>
  </div>
@endsection
@include('admin.subfleets.script')
