@extends('admin.app')
@section('title', 'Edit '.$typerating->name)
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::model($typerating, ['route' => ['admin.typeratings.update', $typerating->id], 'method' => 'patch', 'autocomplete' => false]) }}
      @include('admin.typeratings.fields')
      {{ Form::close() }}
    </div>
  </div>

  <div class="card border-blue-bottom">
    <div class="content">
      <div class="header">
        <h3>Subfleets</h3>
        @component('admin.components.info')
          These are the subfleets this type rating is allowed to use.
        @endcomponent
      </div>
      <div class="row">
        @include('admin.typeratings.subfleets')
      </div>
    </div>
  </div>
@endsection
@include('admin.typeratings.scripts')
