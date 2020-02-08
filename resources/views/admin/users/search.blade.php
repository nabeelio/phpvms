<div class="content">
  <div class="row">
    <div class="col-sm-12">
      <div class="form-group">
        {{ Form::open(['route' => 'admin.users.index', 'method' => 'GET', 'class'=>'form-inline pull-right']) }}

        {{ Form::label('name', 'Name:') }}
        {{ Form::text('name', null, ['class' => 'form-control']) }}

        {{ Form::label('email', 'Email:') }}
        {{ Form::text('email', null, ['class' => 'form-control']) }}
        &nbsp;
        {{--{{ Form::label('dep_icao', 'Departure:') }}
        {{ Form::select('dep_icao', $airports, null , ['class' => 'form-control']) }}
        &nbsp;
        {{ Form::label('arr_icao', 'Arrival:') }}
        {{ Form::select('arr_icao', $airports, null , ['class' => 'form-control']) }}
        &nbsp;--}}
        {{ Form::submit('find', ['class' => 'btn btn-primary']) }}
        &nbsp;
        <a href="{{ route('admin.users.index') }}">clear</a>
        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>
