<div class="content">
  <div class="row">
    <div class="col-sm-12">
      <div class="form-group">
        {{ Form::open(['route' => 'admin.airports.index', 'method' => 'GET', 'class'=>'form-inline pull-right']) }}

        {{ Form::label('icao', 'ICAO:') }}
        {{ Form::text('icao', null, ['class' => 'form-control']) }}
        &nbsp;
        <a href="{{ route('admin.airports.index') }}">clear</a>
        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>
