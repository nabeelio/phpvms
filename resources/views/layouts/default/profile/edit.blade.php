@extends("layouts.${SKIN_NAME}.app")
@section('title', 'edit profile')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="description">edit your profile</h2>
          @include('flash::message')
          {!! Form::model($user, ['route' => ['frontend.profile.update', $user->id], 'method' => 'patch']) !!}
             @include("layouts.${SKIN_NAME}.profile.fields")
          {!! Form::close() !!}
    </div>
</div>
@endsection
