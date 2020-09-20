@extends('admin.app')
@section('title', "Edit \"$rank->name\"")
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::model($rank, ['route' => ['admin.ranks.update', $rank->id], 'method' => 'patch', 'autocomplete' => false]) }}
      @include('admin.ranks.fields')
      {{ Form::close() }}
    </div>
  </div>

  <div class="card border-blue-bottom">
    <div class="content">
      <div class="header">
        <h3>subfleets</h3>
        @component('admin.components.info')
          These are the subfleets this rank is allowed to use. The pay can be
          set as a fixed amount, or a percentage of the rank's base payrate above
        @endcomponent
      </div>
      <div class="row">
        @include('admin.ranks.subfleets')
      </div>
    </div>
  </div>
@endsection
@include('admin.ranks.scripts')
