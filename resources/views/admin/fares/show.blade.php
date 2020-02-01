@extends('admin.app')

@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      @include('admin.fares.show_fields')
    </div>
  </div>
@endsection
