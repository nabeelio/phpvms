<div class="content">
  <div class="row">
    <div class="col-sm-12">
      <div class="form-group">
        {{ Form::open(['route' => 'admin.flights.index', 'method' => 'GET', 'class'=>'form-inline pull-right']) }}
        &nbsp;&nbsp;
        {{ Form::label('airlines', 'Airline:') }}
        {{ Form::select('airline_id', $airlines, null , ['class' => 'form-control select2']) }}
        {{ Form::label('flight_number', 'Flight Number:') }}
        {{ Form::text('flight_number', null, ['class' => 'form-control']) }}
        &nbsp;
        {{ Form::label('dpt_airport_id', 'Departure:') }}
        {{ Form::select('dpt_airport_id', $airports, null , ['class' => 'form-control']) }}
        &nbsp;
        {{ Form::label('arr_airport_id', 'Arrival:') }}
        {{ Form::select('arr_airport_id', $airports, null , ['class' => 'form-control']) }}
        &nbsp;
        {{ Form::submit('find', ['class' => 'btn btn-primary']) }}
        &nbsp;
        <a href="{{ route('admin.flights.index') }}">clear</a>
        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>
