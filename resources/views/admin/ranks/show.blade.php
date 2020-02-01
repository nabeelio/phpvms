@extends('admin.app')

@section('content')
  <section class="content-header">
    <h1>{{ $rank->name }}</h1>
  </section>
  <div class="content">
    <div class="box box-primary">
      <div class="box-body">
        <div class="row" style="padding-left: 20px">
          @include('admin.ranks.show_fields')
        </div>
      </div>
    </div>
  </div>
@endsection
@include('admin.ranks.scripts')
