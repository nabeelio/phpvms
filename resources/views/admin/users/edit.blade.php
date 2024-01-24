@extends('admin.app')
@section('title', 'Edit '. $user->name)
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      {{ Form::model($user, ['route' => ['admin.users.update', $user->id], 'method' => 'patch', 'autocomplete' => false]) }}
      @include('admin.users.fields')

      <div class="row">
        <div class="form-group col-sm-12 text-right">
          {{-- <a href="{{ route('admin.users.regen_apikey', [$user->id]) }}" class="btn btn-warning" onclick="return confirm('Are you sure? This will reset this user\'s API key.')">New API Key</a> --}}
          &nbsp;
          @if (!$user->email_verified_at)
            <a href="{{ route('admin.users.verify_email', [$user->id]) }}" class="btn btn-danger">Verify email manually</a>
          @else
            <a href="{{ route('admin.users.request_email_verification', [$user->id]) }}" class="btn btn-warning">Request new email verification</a>
          @endif

          {{ Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) }}
          <a href="{{ route('admin.users.index') }}" class="btn btn-default">Cancel</a>
        </div>
      </div>
      {{ Form::close() }}

      <div class="row">
        <div class="form-group col-sm-6">
          @include('admin.users.custom_fields')
        </div>
        <div class="form-group col-sm-6">
          @include('admin.users.details')
        </div>
      </div>
    </div>
  </div>

  <div class="card border-blue-bottom">
    <div class="content">
      <div class="header">
        <h3>Type Ratings</h3>
      </div>
      @include('admin.users.type_ratings')
    </div>
  </div>

  <div class="card border-blue-bottom">
    <div class="content">
      <div class="header">
        <h3>Awards</h3>
      </div>
      @include('admin.users.awards')
    </div>
  </div>

  <div class="card border-blue-bottom">
    <div class="content">
      <div class="header">
        <h3>PIREPs</h3>
      </div>

      @include('admin.pireps.table')

      <div class="row">
        <div class="col-12 text-center">
          {{ $pireps->withQueryString()->links('admin.pagination.default') }}
        </div>
      </div>
    </div>
  </div>
@endsection
@include('admin.users.script')
